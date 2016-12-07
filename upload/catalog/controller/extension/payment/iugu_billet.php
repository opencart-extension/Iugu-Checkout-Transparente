<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ControllerExtensionPaymentIuguBillet extends Controller {
	
	public function index() {
		
		$data = $this->language->load('extension/payment/iugu');
		
		/* Gera Boleto */
		$billet = $this->generateBillet();
		
		$data['errors'] = false;
		
		if (isset($billet['errors']) && !empty($billet['errors'])) {
			foreach($billet['errors'] as $key => $error_base) {
				foreach($error_base as $error) {
					$data['errors'][] = ucfirst($key) . ' ' . $error;
				}
			}
		}
		
		/* Captura HTML do boleto */
		if ($data['errors'] === false)
			$data['billet'] = $this->captureBilletHTML($billet['secure_url']);
		else
			$data['billet'] = false;
		
		/* Link Download do Boleto */
		$data['billet_download'] = $this->url->link('extension/payment/iugu/download', '', 'SSL');
		
		/* Link para envio de Email */
		$data['billet_send_mail'] = $this->url->link('extension/payment/iugu/sendMail', '', 'SSL');
		
		/* Mensagem de Sucesso */
		$data['billet_send_mail'] = $this->url->link('extension/payment/iugu/sendMail', '', 'SSL');
		
		$data['continue'] = $this->url->link('extension/payment/iugu_billet/confirm', '', 'SSL');
		
		return $this->load->view('extension/payment/iugu_billet.tpl', $data);
	}
	
	private function generateBillet() {
		
		/* Carrega Model */
		$this->load->model('extension/payment/iugu');
		
		/* Carrega library */
		require_once DIR_SYSTEM . 'library/Iugu.php';
		
		/* Define a API */
		Iugu::setApiKey($this->config->get('iugu_token'));
		
		$data = array();
		
		/* Url de Notificações */
		$data['notification_url'] = $this->url->link('extension/payment/iugu/notification', '', 'SSL');
		
		/* Validade */
		$data['due_date'] = date('d/m/Y', strtotime('+7 days'));
		
		/* Forma de Pagamento */
		$data['payable_with'] = 'bank_slip';
		
		/* Url de Expiração */
		$data['expired_url'] = $this->url->link('extension/payment/iugu/expired', '', 'SSL');
		
		/* Carrega model de pedido */
		$this->load->model('account/order');
		$this->load->model('checkout/order');
		
		/* Captura informações do pedido */
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		/* Captura o E-mail do Cliente */
		$data['email'] = $order_info['email'];
		
		/* Captura os produtos comprados */
		$products = $this->model_account_order->getOrderProducts($this->session->data['order_id']);
		
		/* Formata as informações do produto (Nome, Quantidade e Preço unitário) */
		$data['items'] = array();
		
		$count = 0;
		
		foreach($products as $product) {
			$data['items'][$count] = array(
				'description' => $product['name'],
				'quantity' => $product['quantity'],
				'price_cents' => $this->currency->format($product['price'], $this->session->data['currency'], null, false) * 100
			);
			$count++;
		}
		
		unset($count);
		
		/* Captura os Descontos, Acréscimo, Vale-Presente, Crédito do Cliente, etc. */
		$data['items'] = array_merge($data['items'], $this->model_extension_payment_iugu->getTotals());
		
		/* Captura valor do desconto */
		$data['discount_cents'] = $this->model_extension_payment_iugu->getDiscount();
		
		/* Informações do Cliente */
		$data['payer'] = array();
		$data['payer']['cpf_cnpj'] = isset($order_info['custom_field'][$this->config->get('iugu_custom_field_cpf')]) ? $order_info['custom_field'][$this->config->get('iugu_custom_field_cpf')] : '';
		$data['payer']['name'] = $order_info['firstname'] . ' ' . $order_info['lastname'];
		$data['payer']['phone_prefix'] = substr($order_info['telephone'], 0, 2);
		$data['payer']['phone'] = substr($order_info['telephone'], 2);
		$data['payer']['email'] = $order_info['email'];
		
		/* Informações de Endereço */
		$data['payer']['address'] = array();
		$data['payer']['address']['street'] = $order_info['payment_address_1'];
		$data['payer']['address']['number'] = isset($order_info['payment_custom_field'][$this->config->get('iugu_custom_field_number')]) ? $order_info['payment_custom_field'][$this->config->get('iugu_custom_field_number')] : 0;
		$data['payer']['address']['city'] = $order_info['payment_city'];
		$data['payer']['address']['state'] = $order_info['payment_zone_code'];
		$data['payer']['address']['country'] = $order_info['payment_country'];
		$data['payer']['address']['zip_code'] = $order_info['payment_postcode'];
		
		/* Informações adicionais */
		$data['custom_variables'] = array(
			array(
				'name' => 'order_id',
				'value' => $this->session->data['order_id']
			)
		);
		
		/* Envia informações */
		$token = Iugu_Invoice::create($data);
		
		$result = array();
		
		foreach(reset($token) as $key => $value) {
			$result[$key] = $value;
		}
		
		$this->session->data['result_iugu'] = $result;
		
		return $result;
	}

	/*
		Method: captureBilletHTML
		Function: Captura HTML do boleto
		Parameters:  $url    String    required
		Return: String
	*/
	private function captureBilletHTML($url) {
		$html = file_get_contents($url);
		preg_match("/<div\s+?class='bank_slip_form'\+?>(.*)<\/div>/s", $html, $newHtml);
		$newHtml = str_replace('/assets/', 'https://iugu.com/assets/', $newHtml[0]);
		$newHtml = preg_replace('/>\s+/', '>', $newHtml);
		return preg_replace('/(<\/div>){3}$/', '', $newHtml);
	}
	
	/*
		Method: confirm
		Function: Armazena os dados do pedido na loja
	*/
	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'iugu_billet') {
			$this->load->model('checkout/order');
			$this->load->model('extension/payment/iugu');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('iugu_order_status_pending'));
			
			$this->model_payment_iugu->addOrder($this->session->data['order_id'], $this->session->data['result_iugu']);
			
			$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
			
			unset($this->session->data['result_iugu']);
		}
	}
}