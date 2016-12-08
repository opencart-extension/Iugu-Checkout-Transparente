<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ControllerExtensionPaymentIugu extends Controller {
	
	/*
		Method: sendMail
		Function: Envia e-mail com o boleto
		Return: Bool
	*/
	public function sendMail() {
		if (isset($this->session->data['result_iugu'])) {
			$this->language->load('extension/payment/iugu');
			
			if (isset($this->session->data['result_iugu']['secure_url']))
				$file = $this->session->data['result_iugu']['secure_url'] . '.pdf';
			else
				$file = $this->session->data['result_iugu']['pdf'];
			
			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			$mail->setTo($this->session->data['result_iugu']['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject(sprintf($this->language->get('text_mail_subject'), $this->config->get('config_name')));
			$mail->setHtml($this->language->get('text_mail_html'));
			$mail->setText($this->language->get('text_mail_text'));
			$mail->addAttachment($file);
			return $mail->send();
		}
	}
	
	/*
		Method: download
		Function: Faz o download do boleto
	*/
	public function download() {
		if (isset($this->session->data['result_iugu'])) {
			if (isset($this->session->data['result_iugu']['secure_url']))
				$file = $this->session->data['result_iugu']['secure_url'] . '.pdf';
			else
				$file = $this->session->data['result_iugu']['pdf'];
			
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $this->config->get('config_name') . '.pdf"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			//header('Content-Length: ' . filesize($file));

			if (ob_get_level()) {
				ob_end_clean();
			}

			readfile($file, 'rb');
		}
	}
	
	
	/*
		Method: notification
		Function: Atualiza a situação de pagamento do pedido
	*/
	public function notification() {
		if (isset($this->request->post)) {
			$event = $this->request->post['event'];
			$data = $this->request->post['data'];
			
			$this->load->model('extension/payment/iugu');
			
			$this->model_payment_iugu->updateOrderHistory($data['id'], $data['status']);
		}
		
	}
	
	/*
		Method: expired
		Function: Cria uma nova fatura e envia um e-mail para o cliente um lembrete
	*/
	public function expired() {
		if (isset($this->request->post)) {
			$event = $this->request->post['event'];
			$data = $this->request->post['data'];
			
			$this->load->model('extension/payment/iugu');
			
			$order_id = $this->model_extension_payment_iugu->updateOrderHistory($data['id'], $data['status']);
			
			if ($order_id !== false) {
				/* Carrega Model */
				$this->load->model('extension/payment/iugu');
				
				/* Carrega library */
				require_once DIR_SYSTEM . 'library/Iugu.php';
				
				/* Define a API */
				Iugu::setApiKey($this->config->get('iugu_token'));
				
				$data = array();
				
				/* Método de Pagamento (somente boleto) */
				$data['payable_with '] = $this->config->get('iugu_reminder_payment_method');
				
				/* Url de Notificações */
				$data['notification_url'] = $this->url->link('payment/iugu/notification', '', 'SSL');
				
				/* Url de Expiração */
				$data['expired_url'] = $this->url->link('payment/iugu/expired', '', 'SSL');
				
				/* Validade */
				$data['due_date'] = date('d/m/Y', strtotime('+7 days'));
				
				/* Captura informações do pedido */
				$order_info = $this->model_extension_payment_iugu->getOrder($order_id);
				
				/* Captura o E-mail do Cliente */
				$data['email'] = $order_info['email'];
				
				/* Captura os produtos comprados */
				$products = $this->model_extension_payment_iugu->getOrderProducts($order_id);
				
				/* Formata as informações do produto (Nome, Quantidade e Preço unitário) */
				$data['items'] = array();
				
				$count = 0;
				
				foreach($products as $product) {
					$data['items'][$count] = array(
						'description' => $product['name'],
						'quantity' => $product['quantity'],
						'price_cents' => $this->currency->format($product['price'], 'BRL', null, false) * 100
					);
					$count++;
				}
				
				$totals = $this->model_extension_payment_iugu->getOrderTotals($order_id);
				
				foreach($totals as $total) {
					if ($total['code'] != 'sub_total' && $total['code'] != 'total') {
						$data['items'][$count] = array(
							'description' => $total['title'],
							'quantity' => 1,
							'price_cents' => $total['value'] * 100,
						);
						$count++;
					}
				}
				
				unset($count);
				
				/* Captura os Descontos, Acréscimo, Vale-Presente, Crédito do Cliente, etc. */
				$data['items'] = $data['items'];
				
				/* Captura valor do desconto */
				$sub_total = 0;
				
				foreach($totals as $total){
					if ($total['code'] == 'sub_total') {
						$sub_total = $total['value'];
						break;
					}
				}
				$data['discount_cents'] = $this->model_extension_payment_iugu->getDiscount($sub_total, $this->config->get('iugu_reminder_payment_method'));
				
				/* Captura valor do acréscimo */
				$data['tax_cents'] = $this->model_extension_payment_iugu->getInterest($sub_total, $this->config->get('iugu_reminder_payment_method'));
				
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
						'value' => $order_id
					)
				);
				
				/* Envia informações */
				try {
					$token = Iugu_Invoice::create($data);
				} catch (Exception $e) {
					$this->log->write($e->getMessage());
					die();
				}
				
				$result = array();
				
				foreach(reset($token) as $key => $value) {
					$result[$key] = $value;
				}
				
				if (isset($result['errors']) && !empty($result['errors'])) {
					foreach($result['errors'] as $key => $error_base) {
						foreach($error_base as $error) {
							$this->log->write('Iugu: ' . ucfirst($key) . ' ' . $error);
						}
					}
				} else {
					$this->model_extension_payment_iugu->updateOrder($order_id, $result);
					
					$data = array_merge($this->language->load('mail/iugu'), $result);
					
					$mail = new Mail();
					$mail->protocol = $this->config->get('config_mail_protocol');
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
					$mail->setTo($order_info['email']);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender($this->config->get('config_name'));
					$mail->setSubject(sprintf($this->language->get('text_mail_subject_expired'), $this->config->get('config_name')));
					$mail->setHtml($this->getHtml($order_info, $products, $totals));
					
					$mail->setText($this->language->get('text_mail_text'));
					return $mail->send();
				}
			} else {
				$this->log->write('Iugu: Invoice ' . $data['id'] . ' não localizado.');
			}
		}
	}

	/*
		Method: getHtml
		Parameters: 
			$order_info  (array)  Informações do Pedidp
			$products    (array)  Produtos comprados
			$totals      (array)  Total do pedido
		Function: Cria HTML
		Return: String
	*/
	private function getHtml($order_info, $products, $totals) {
		
		/* Carrega model de pedido */
		$this->load->model('extension/payment/iugu');
		
		$data = $this->language->load('mail/iugu');
		
		$data['text_message_1'] 	= sprintf($this->language->get('text_message_1'), $order_info['firstname'], $order_info['lastname'], $order_info['order_id']);
		$data['text_details_order'] = sprintf($this->language->get('text_details_order'), $order_info['order_id'], date($this->language->get('date_format_short'), strtotime($order_info['date_added'])));
		$data['text_payment_method'] = sprintf($this->language->get('text_payment_method'), $order_info['payment_method']);
		$data['text_shipping_method'] = sprintf($this->language->get('text_shipping_method'), $order_info['shipping_method']);
		
		$data['store_logo'] = HTTPS_SERVER . 'image/' . $this->config->get('config_logo');
		
		$data['store_name'] = $order_info['store_name'];
		$data['store_email'] = $this->config->get('config_email');
		$data['store_url'] = $order_info['store_url'];
		
		$data['firstname'] = $order_info['firstname'];
		$data['lastname'] = $order_info['lastname'];
		
		$data['order_id'] = $order_info['order_id'];
		$data['currency_code'] = $order_info['currency_code'];
		$data['total'] = $this->currency->format($order_info['total']);
		$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
		
		$data['payment_method'] = $order_info['payment_method'];
		$data['shipping_method'] = $order_info['shipping_method'];
		
		if (!empty($order_info['payment_address_format'])) {
			$format = $order_info['payment_address_format'];
		} else {
			$format = '{firstname} {lastname}<br/>{address_1}<br/>{address_2}<br/>{city}, {postcode}<br/>{zone}<br/>{country}';
		}
		
		$search = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{zone}',
			'{postcode}',
			'{country}'
		);
		
		$replace = array(
			$order_info['payment_firstname'],
			$order_info['payment_lastname'],
			$order_info['payment_company'],
			$order_info['payment_address_1'],
			$order_info['payment_address_2'],
			$order_info['payment_city'],
			$order_info['payment_zone'],
			$order_info['payment_postcode'],
			$order_info['payment_country'],
		);
		
		$data['payment_address_format'] = str_replace($search, $replace, $format);
		
		if (!empty($order_info['shipping_address_format'])) {
			$format = $order_info['shipping_address_format'];
		} else {
			$format = '{firstname} {lastname}<br/>{address_1}<br/>{address_2}<br/>{city}, {postcode}<br/>{zone}<br/>{country}';
		}
		
		$search = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{zone}',
			'{postcode}',
			'{country}'
		);
		
		$replace = array(
			$order_info['shipping_firstname'],
			$order_info['shipping_lastname'],
			$order_info['shipping_company'],
			$order_info['shipping_address_1'],
			$order_info['shipping_address_2'],
			$order_info['shipping_city'],
			$order_info['shipping_zone'],
			$order_info['shipping_postcode'],
			$order_info['shipping_country'],
		);
		
		$data['shipping_address_format'] = str_replace($search, $replace, $format);
		
		$this->load->model('tool/image');
		
		$data['products'] = array();
		
		foreach($products as $product) {
			$data['products'][] = array(
				'name'     => $product['name'],
				'model'    => $product['model'],
				'quantity' => $product['quantity'],
				'price'    => $this->currency->format($product['price']),
				'total'    => $this->currency->format($product['total'])
			);
		}
		
		$data['totals'] = array();
		
		foreach($totals as $total) {
			$data['totals'][] = array(
				'title' => $total['title'],
				'value' => $this->currency->format($total['value'])
			);
		}
		
		$data['schema'] = $this->buildSchema($order_info, $products);
		
		$data['continue'] = $this->url->link('payment/iugu_pay', 'order_id=' . md5($order_info['email']) . '-' . $order_info['order_id'] . '-' . md5($order_info['order_id']), true);
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/iugu.tpl'))
			return $this->load->view($this->config->get('config_template') . '/template/mail/iugu.tpl', $data);
		else
			return $this->load->view('default/template/mail/iugu.tpl', $data);
	}
	
	
	/*
		Method: buildSchema
		Parameters: 
			$order_info  (array)  Informações do Pedidp
			$products    (array)  Produtos comprados
		Function: Cria dados estruturados para o Google
		Return: json
	*/
	private function buildSchema($order_info, $products) {
		$data = array();
	
		$data['@context'] = 'http://schema.org';
		$data['@type'] = 'Order';
		
		$data['acceptedOffer'] = array();
		
		foreach($products as $key => $product) {
			$data['acceptedOffer'][$key]['@type'] = 'Offer';
			$data['acceptedOffer'][$key]['itemOffered'] = array();
			$data['acceptedOffer'][$key]['itemOffered']['name'] = $product['name'];
			$data['acceptedOffer'][$key]['itemOffered']['image'] = $this->model_tool_image->resize($product['image'], 800, 800);
			$data['acceptedOffer'][$key]['itemOffered']['sku'] = $product['model'];
			$data['acceptedOffer'][$key]['itemOffered']['url'] = $this->url->link('product/product', 'product_id=' . $product['product_id']);
			$data['acceptedOffer'][$key]['priceCurrency'] = $order_info['currency_code'];
			$data['acceptedOffer'][$key]['price'] = $product['price'];
		}
		
		$data['merchant'] = array();
		$data['merchant']['@type'] = 'Organization';
		$data['merchant']['name'] = $order_info['store_name'];
		
		$data['orderNumber'] = $order_info['order_id'];
		$data['orderDate'] = date('Y-m-d', strtotime($order_info['date_added']));
		$data['price'] = $this->currency->format($order_info['total']);
		$data['priceCurrency'] = $order_info['currency_code'];
		$data['orderStatus'] = 'OrderProblem';
		$data['url'] = $this->url->link('account/order/info', 'order_id=' . $order_info['order_id']);
		
		$data['customer'] = array();
		$data['customer']['@type'] = 'Person';
		$data['customer']['name'] = $order_info['firstname'] . ' ' . $order_info['lastname'];
		
		$data['billingAddress'] = array();
		$data['billingAddress']['@type'] = 'PostalAddress';
		$data['billingAddress']['streetAddress'] = $order_info['payment_address_1'] . ',' . $order_info['payment_address_2'] . ',' . $order_info['payment_city'];
		$data['billingAddress']['addressRegion'] = $order_info['payment_zone'];
		$data['billingAddress']['postalCode'] = $order_info['payment_postcode'];
		
		$data['billingAddress']['addressCountry'] = array();
		$data['billingAddress']['addressCountry']['@type'] = 'Country';
		$data['billingAddress']['addressCountry']['name'] = $order_info['payment_country'];
	  
		$data['PriceSpecification'] = array();
		$data['PriceSpecification']['@type'] = 'PriceSpecification';
		$data['PriceSpecification']['price'] = $this->currency->format($order_info['total']);
		$data['PriceSpecification']['priceCurrency'] = $order_info['currency_code'];
	  
		return json_encode($data);
	}
}
