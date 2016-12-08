<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ControllerExtensionPaymentIuguCreditCard extends Controller {
	
	public function index() {
		/* Carrega o idioma */
		$data = $this->language->load('extension/payment/iugu');
		
		/* Carrega model */
		$this->load->model('extension/payment/iugu');
		
		/* Captura o ID da Conta */
		$data['iugu_account_id'] = $this->config->get('iugu_account_id');
		
		/* Verifica se é modo de teste */
		$data['test_mode'] = (bool)$this->config->get('iugu_test_mode');
		
		/* Calcula o valor das parcelas */
		$data['installments'] = $this->model_extension_payment_iugu->getInstallments();
		
		/* Link de pagamento */
		$data['link_pay'] = $this->url->link('extension/payment/iugu_credit_card/pay', '', 'SSL');
		
		/* Link Download da Fatura */
		$data['link_download_invoice'] = $this->url->link('extension/payment/iugu/download', '', 'SSL');
		
		/* Link Envia Fatura por E-mail */
		$data['link_send_mail_invoice'] = $this->url->link('extension/payment/iugu/sendMail', '', 'SSL');
		
		/* Link Confirmação do Pedido */
		$data['continue'] = $this->url->link('extension/payment/iugu_credit_card/confirm', '', 'SSL');
		
		return $this->load->view('extension/payment/iugu_credit_card.tpl', $data);
	}
	
	public function pay() {
		
		/* Carrega Model */
		$this->load->model('extension/payment/iugu');
		
		/* Carrega library */
		require_once DIR_SYSTEM . 'library/Iugu.php';
		
		/* Define a API */
		Iugu::setApiKey($this->config->get('iugu_token'));
		
		$data = array();
		
		/* Recebe o token gerado */
		$data['token'] = isset($this->request->post['token']) ? $this->request->post['token'] : '';
		
		/* Recebe a quantidade de parcelas */
		$data['months'] = ($this->config->get('iugu_credit_card_installments_status') || !isset($this->request->post['installment'])) ? $this->request->post['installment'] : 1;

		/* Forma de Pagamento */
		$data['payable_with'] = 'credit_card';
		
		/* Url de Notificaões */
		$data['notification_url'] = $this->url->link('extension/payment/iugu/notification', '', 'SSL');
		
		/* Url de Expiração */
		$data['expired_url'] = $this->url->link('extension/payment/iugu/expired', '', 'SSL');
		
		/* Validade */
		$data['due_date'] = date('d/m/Y', strtotime('+7 days'));
		
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
			'order_id' => $this->session->data['order_id']
		);
		
		/* Envia informações */
		$result = Iugu_Charge::create($data);
		
		$response = array();
		
		foreach(reset($result) as $key => $value) {
			$response[$key] = $value;
		}
		
		$this->session->data['result_iugu'] = $response;
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	/*
		Method: confirm
		Function: Armazena os dados do pedido na loja
	*/
	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'iugu_credit_card') {
			$this->load->model('checkout/order');
			$this->load->model('extension/payment/iugu');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('iugu_order_status_pending'));
			
			$this->model_extension_payment_iugu->addOrder($this->session->data['order_id'], $this->session->data['result_iugu'], true);
			
			$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
			
			unset($this->session->data['result_iugu']);
		}
	}
}