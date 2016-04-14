<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ControllerPaymentIugu extends Controller {
	
	private $error = array();
	
	public function index() {
		$data = $this->load->language('payment/iugu');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('iugu', $this->request->post);
			
			$this->load->model('extension/extension');
			
			if ($this->request->post['iugu_credit_card_status']) {
				$this->model_extension_extension->install('payment', 'iugu_credit_card');
			} else {
				$this->model_extension_extension->uninstall('payment', 'iugu_credit_card');
			}
			
			if ($this->request->post['iugu_billet_status']) {
				$this->model_extension_extension->install('payment', 'iugu_billet');
			} else {
				$this->model_extension_extension->uninstall('payment', 'iugu_billet');
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = false;
		}
		
		if (isset($this->error['iugu_tax'])) {
			$data['error_iugu_tax'] = $this->error['iugu_tax'];
		} else {
			$data['error_iugu_tax'] = false;
		}
		
		$data['breadcrumbs'] = array();
		
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('common/dashboard'),
			'text' => $this->language->get('text_home')
		);
		
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('extension/payment'),
			'text' => $this->language->get('text_payment_method')
		);
		
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('payment/iugu'),
			'text' => $this->language->get('heading_title')
		);
		
		if (isset($this->request->post['iugu_status'])) {
			$data['iugu_status'] = $this->request->post['iugu_status'];
		} else {
			$data['iugu_status'] = $this->config->get('iugu_status');
		}
		
		if (isset($this->request->post['iugu_account_id'])) {
			$data['iugu_account_id'] = $this->request->post['iugu_account_id'];
		} else {
			$data['iugu_account_id'] = $this->config->get('iugu_account_id');
		}
		
		if (isset($this->request->post['iugu_token'])) {
			$data['iugu_token'] = $this->request->post['iugu_token'];
		} else {
			$data['iugu_token'] = $this->config->get('iugu_token');
		}
		
		if (isset($this->request->post['iugu_test_mode'])) {
			$data['iugu_test_mode'] = $this->request->post['iugu_test_mode'];
		} else {
			$data['iugu_test_mode'] = $this->config->get('iugu_test_mode');
		}

		if (isset($this->request->post['iugu_custom_field_number'])) {
			$data['iugu_custom_field_number'] = $this->request->post['iugu_custom_field_number'];
		} else {
			$data['iugu_custom_field_number'] = $this->config->get('iugu_custom_field_number');
		}

		if (isset($this->request->post['iugu_custom_field_cpf'])) {
			$data['iugu_custom_field_cpf'] = $this->request->post['iugu_custom_field_cpf'];
		} else {
			$data['iugu_custom_field_cpf'] = $this->config->get('iugu_custom_field_cpf');
		}

		if (isset($this->request->post['iugu_geo_zone_id'])) {
			$data['iugu_geo_zone_id'] = $this->request->post['iugu_geo_zone_id'];
		} else {
			$data['iugu_geo_zone_id'] = $this->config->get('iugu_geo_zone_id');
		}

		if (isset($this->request->post['iugu_sort_order'])) {
			$data['iugu_sort_order'] = $this->request->post['iugu_sort_order'];
		} else {
			$data['iugu_sort_order'] = $this->config->get('iugu_sort_order');
		}

		if (isset($this->request->post['iugu_reminder_invoice_status'])) {
			$data['iugu_reminder_invoice_status'] = $this->request->post['iugu_reminder_invoice_status'];
		} else {
			$data['iugu_reminder_invoice_status'] = $this->config->get('iugu_reminder_invoice_status');
		}

		if (isset($this->request->post['iugu_reminder_discount'])) {
			$data['iugu_reminder_discount'] = $this->request->post['iugu_reminder_discount'];
		} else {
			$data['iugu_reminder_discount'] = $this->config->get('iugu_reminder_discount');
		}

		if (isset($this->request->post['iugu_reminder_interest'])) {
			$data['iugu_reminder_interest'] = $this->request->post['iugu_reminder_interest'];
		} else {
			$data['iugu_reminder_interest'] = $this->config->get('iugu_reminder_interest');
		}

		if (isset($this->request->post['iugu_reminder_expiration'])) {
			$data['iugu_reminder_expiration'] = $this->request->post['iugu_reminder_expiration'];
		} else {
			$data['iugu_reminder_expiration'] = $this->config->get('iugu_reminder_expiration');
		}

		if (isset($this->request->post['iugu_reminder_payment_method'])) {
			$data['iugu_reminder_payment_method'] = $this->request->post['iugu_reminder_payment_method'];
		} else {
			$data['iugu_reminder_payment_method'] = $this->config->get('iugu_reminder_payment_method');
		}

		if (isset($this->request->post['iugu_credit_card_status'])) {
			$data['iugu_credit_card_status'] = $this->request->post['iugu_credit_card_status'];
		} else {
			$data['iugu_credit_card_status'] = $this->config->get('iugu_credit_card_status');
		}

		if (isset($this->request->post['iugu_credit_card_discount'])) {
			$data['iugu_credit_card_discount'] = $this->request->post['iugu_credit_card_discount'];
		} else {
			$data['iugu_credit_card_discount'] = $this->config->get('iugu_credit_card_discount');
		}

		if (isset($this->request->post['iugu_credit_card_interest'])) {
			$data['iugu_credit_card_interest'] = $this->request->post['iugu_credit_card_interest'];
		} else {
			$data['iugu_credit_card_interest'] = $this->config->get('iugu_credit_card_interest');
		}

		if (isset($this->request->post['iugu_credit_card_installments_status'])) {
			$data['iugu_credit_card_installments_status'] = $this->request->post['iugu_credit_card_installments_status'];
		} else {
			$data['iugu_credit_card_installments_status'] = $this->config->get('iugu_credit_card_installments_status');
		}

		if (isset($this->request->post['iugu_qnt_max_installments'])) {
			$data['iugu_qnt_max_installments'] = $this->request->post['iugu_qnt_max_installments'];
		} else {
			$data['iugu_qnt_max_installments'] = $this->config->get('iugu_qnt_max_installments');
		}

		if (isset($this->request->post['iugu_qnt_installments_interest_free'])) {
			$data['iugu_qnt_installments_interest_free'] = $this->request->post['iugu_qnt_installments_interest_free'];
		} else {
			$data['iugu_qnt_installments_interest_free'] = $this->config->get('iugu_qnt_installments_interest_free');
		}

		if (isset($this->request->post['iugu_credit_card_tax'])) {
			$data['iugu_credit_card_tax'] = $this->request->post['iugu_credit_card_tax'];
		} else {
			$data['iugu_credit_card_tax'] = $this->config->get('iugu_credit_card_tax');
		}

		if (isset($this->request->post['iugu_billet_status'])) {
			$data['iugu_billet_status'] = $this->request->post['iugu_billet_status'];
		} else {
			$data['iugu_billet_status'] = $this->config->get('iugu_billet_status');
		}

		if (isset($this->request->post['iugu_billet_discount'])) {
			$data['iugu_billet_discount'] = $this->request->post['iugu_billet_discount'];
		} else {
			$data['iugu_billet_discount'] = $this->config->get('iugu_billet_discount');
		}

		if (isset($this->request->post['iugu_billet_interest'])) {
			$data['iugu_billet_interest'] = $this->request->post['iugu_billet_interest'];
		} else {
			$data['iugu_billet_interest'] = $this->config->get('iugu_billet_interest');
		}

		if (isset($this->request->post['iugu_order_status_draft'])) {
			$data['iugu_order_status_draft'] = $this->request->post['iugu_order_status_draft'];
		} else {
			$data['iugu_order_status_draft'] = $this->config->get('iugu_order_status_draft');
		}

		if (isset($this->request->post['iugu_order_status_pending'])) {
			$data['iugu_order_status_pending'] = $this->request->post['iugu_order_status_pending'];
		} else {
			$data['iugu_order_status_pending'] = $this->config->get('iugu_order_status_pending');
		}

		if (isset($this->request->post['iugu_order_status_partially_paid'])) {
			$data['iugu_order_status_partially_paid'] = $this->request->post['iugu_order_status_partially_paid'];
		} else {
			$data['iugu_order_status_partially_paid'] = $this->config->get('iugu_order_status_partially_paid');
		}

		if (isset($this->request->post['iugu_order_status_paid'])) {
			$data['iugu_order_status_paid'] = $this->request->post['iugu_order_status_paid'];
		} else {
			$data['iugu_order_status_paid'] = $this->config->get('iugu_order_status_paid');
		}

		if (isset($this->request->post['iugu_order_status_canceled'])) {
			$data['iugu_order_status_canceled'] = $this->request->post['iugu_order_status_canceled'];
		} else {
			$data['iugu_order_status_canceled'] = $this->config->get('iugu_order_status_canceled');
		}

		if (isset($this->request->post['iugu_order_status_refunded'])) {
			$data['iugu_order_status_refunded'] = $this->request->post['iugu_order_status_refunded'];
		} else {
			$data['iugu_order_status_refunded'] = $this->config->get('iugu_order_status_refunded');
		}

		if (isset($this->request->post['iugu_order_status_expired'])) {
			$data['iugu_order_status_expired'] = $this->request->post['iugu_order_status_expired'];
		} else {
			$data['iugu_order_status_expired'] = $this->config->get('iugu_order_status_expired');
		}
		
		$this->load->model('customer/custom_field');
		
		$data['custom_fields'] = $this->model_customer_custom_field->getCustomFields();
		
		$data['add_custom_field'] = $this->url->link('customer/custom_field/add', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('localisation/geo_zone');
		
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$data['action'] = $this->url->link('payment/iugu', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('payment/iugu.tpl', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/iugu'))
			$this->error['warning'] = $this->language->get('error_warning');
		
		if (($this->request->post['iugu_credit_card_status']) && empty($this->request->post['iugu_credit_card_tax']))
			$this->error['iugu_tax'] = $this->language->get('error_iugu_tax');
		
		return !$this->error;
	}
    
    public function install(){
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` (`type`, `code`) VALUES ('payment', 'iugu_billet') ");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` (`type`, `code`) VALUES ('payment', 'iugu_credit_card') ");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` (`type`, `code`) VALUES ('total', 'iugu_discount') ");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` (`type`, `code`) VALUES ('total', 'iugu_interest') ");
	}
    
    public function uninstall(){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'iugu_billet' ");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'iugu_credit_card' ");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'iugu_discount' ");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'iugu_interest' ");
	}
}