<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ModelPaymentIugu extends Model {
	public function getMethod($address, $total) {
		return array();
	}
	
	public function addOrder($order_id, $data, $credit_card = false) {
		if (!$credit_card)
			$this->db->query('INSERT INTO ' . DB_PREFIX . 'order_iugu SET order_id = "' . (int)$order_id . '", invoice_id = "' . $this->db->escape($data['id']) . '", pdf = "' . $this->db->escape($data['secure_url'] . '.pdf') . '", custom_variables = "' . $this->db->escape(serialize($data['custom_variables'])) . '", identification = "' . $this->db->escape($data['secure_id']) . '", date_added = NOW(), date_modified = NOW()');
		else
			$this->db->query('INSERT INTO ' . DB_PREFIX . 'order_iugu SET order_id = "' . (int)$order_id . '", invoice_id = "' . $this->db->escape($data['invoice_id']) . '", pdf = "' . $this->db->escape($data['pdf']) . '", custom_variables = "' . $this->db->escape(serialize($data['custom_variables'])) . '", identification = "' . $this->db->escape($data['identification']) . '", date_added = NOW(), date_modified = NOW()');
	}
	
	public function updateOrder($order_id, $data) {
		$this->db->query('UPDATE ' . DB_PREFIX . 'order_iugu SET invoice_id = "' . $this->db->escape($data['id']) . '", pdf = "' . $this->db->escape($data['secure_url'] . '.pdf') . '", custom_variables = "' . $this->db->escape(serialize($data['custom_variables'])) . '", identification = "' . $this->db->escape($data['secure_id']) . '", date_modified = NOW() WHERE order_id = "' . (int)$order_id . '"');
	}
	
	public function updateOrderHistory($invoice_id, $order_status) {
		
		switch($order_status) {
			case 'draft':
				$order_status_id = $this->config->get('iugu_order_status_draft');
				break;
				
			case 'pending':
				$order_status_id = $this->config->get('iugu_order_status_pending');
				break;
				
			case 'partially_paid':
				$order_status_id = $this->config->get('iugu_order_status_partially_paid');
				break;
				
			case 'paid':
				$order_status_id = $this->config->get('iugu_order_status_paid');
				break;
				
			case 'canceled':
				$order_status_id = $this->config->get('iugu_order_status_canceled');
				break;
				
			case 'refunded':
				$order_status_id = $this->config->get('iugu_order_status_refunded');
				break;
				
			case 'expired':
				$order_status_id = $this->config->get('iugu_order_status_expired');
				break;
			
			default:
				$order_status_id = $this->config->get('iugu_order_status_pending');
				break;
		}

		$order_info = $this->db->query('SELECT order_id FROM ' . DB_PREFIX . 'order_iugu WHERE invoice_id = "' . $this->db->escape($invoice_id) . '"');
		
		if ($order_info->num_rows) {
			$this->db->query('UPDATE ' . DB_PREFIX . 'order SET order_status_id = "' . (int)$order_status_id . '" WHERE order_id = "' . (int)$order_info->row['order_id'] . '"');
			
			$this->db->query('INSERT INTO ' . DB_PREFIX . 'order_history SET order_id = "' . (int)$order_info->row['order_id'] . '", order_status_id = "' . (int)$order_status_id . '", notify = "0", comment = "", date_added = NOW()');
			
			return (int)$order_info->row['order_id'];
		} else {
			return false;
		}
	}
	
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND order_status_id > '0'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']
			);
		} else {
			return false;
		}
	}
	
	public function getTotals() {
		$this->load->model('extension/extension');
		
		$totals = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();
        
        $total_data = array(
            'totals' => &$totals,
            'total' => &$total,
            'taxes' => &$taxes,
        );

		$results = $this->model_extension_extension->getExtensions('total');

		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('total/' . $result['code']);

				$this->{'model_total_' . $result['code']}->getTotal($total_data);
			}
		}

		$sort_order = array();

        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);
		
		$data = array();

		foreach ($total_data['totals'] as $key => $total) {
            if (($total['code'] != 'total') && ($total['code'] != 'sub_total') && ($total['code'] != 'iugu_discount')) {
				$count = $this->cart->countProducts() + $key;
				$data[$count] = array(
					'description' => $total['title'],
					'quantity' => 1,
					'price_cents'  => $this->currency->format($total['value'], $this->session->data['currency'], null, false) * 100
				);
			}
		}
		
		return $data;
	}

	public function getDiscount($sub_total = null, $payment_method = null) {
		$status = false;
		
		if ($sub_total === null)
			$sub_total = $this->cart->getSubTotal();
		
		if ($payment_method === null)
			$payment_method = isset($this->session->data['payment_method']['code']) ? $this->session->data['payment_method']['code'] : null;
		
		if (!is_null($payment_method) && ($payment_method == 'iugu_credit_card' || $payment_method == 'credit_card')) {
			$settings = $this->config->get('iugu_credit_card_discount');
			
			if ($settings['value'] > 0) {
				$status = true;
			}
		}
		
		if (!is_null($payment_method) && ($payment_method == 'iugu_billet' || $payment_method == 'bank_slip')) {
			$settings = $this->config->get('iugu_billet_discount');
			
			if ($settings['value'] > 0) {
				$status = true;
			}
		}
		
		if (!is_null($payment_method) && $payment_method == 'all') {
			$settings = $this->config->get('iugu_reminder_discount');
			
			if ($settings['value'] > 0) {
				$status = true;
			}
		}
		
		if ($status) {
			$this->load->language('total/iugu');

			if ($settings['type'] == 'F') {
				$discount = $settings['value'];
			} else {
				$discount = ($sub_total/100) * $settings['value'];
			}

			if ($discount > 0) {
				return $discount * 100;
			}
		}
		
		return 0;
	}
	
	public function getInterest($sub_total = null, $payment_method = null) {
		$status = false;
		
		if ($sub_total === null)
			$sub_total = $this->cart->getSubTotal();
		
		if ($payment_method === null)
			$payment_method = isset($this->session->data['payment_method']['code']) ? $this->session->data['payment_method']['code'] : null;
		
		if (!is_null($payment_method) && ($payment_method == 'iugu_credit_card' || $payment_method == 'credit_card')) {
			$settings = $this->config->get('iugu_credit_card_discount');
			
			if ($settings['value'] > 0) {
				$status = true;
			}
		}
		
		if (!is_null($payment_method) && ($payment_method == 'iugu_billet' || $payment_method == 'bank_slip')) {
			$settings = $this->config->get('iugu_billet_discount');
			
			if ($settings['value'] > 0) {
				$status = true;
			}
		}
		
		if (!is_null($payment_method) && $payment_method == 'all') {
			$settings = $this->config->get('iugu_reminder_discount');
			
			if ($settings['value'] > 0) {
				$status = true;
			}
		}
		
		if ($status) {
			$this->load->language('total/iugu');

			if ($settings['type'] == 'F') {
				$discount = $settings['value'];
			} else {
				$discount = ($sub_total/100) * $settings['value'];
			}

			if ($discount > 0) {
				return $discount * 100;
			}
		}
		
		return 0;
	}

	public function getInstallments() {
		$status = $this->config->get('iugu_credit_card_installments_status');
		
		/* Verifica se parcelamento está habilitado */
		if ($status) {
			
			/* Total de Parcelas */
			$installments_total = $this->config->get('iugu_qnt_max_installments');
			
			/* Total de Parcelas sem juros */
			$installments_interest_free = $this->config->get('iugu_qnt_installments_interest_free');
			
			/* Taxa por parcela */
			$installments_interest = array(
				1  => 7,
				2  => 10,
				3  => 11,
				4  => 12,
				5  => 13,
				6  => 15,
				7  => 16,
				8  => 17,
				9  => 18,
				10 => 20,
				11 => 21,
				12 => 22
			);
			
			$installments = array();
			
			/* Calcula os parcelamentos */
			foreach($installments_interest as $key => $interest){
				if ($key <= $installments_total) {
					if ($installments_total > $installments_interest_free) {
						
						/* Valor total da parcela */
						$installment_total = $this->getOrderTotal($this->session->data['order_id']) * ((1 - ($this->config->get('iugu_credit_card_tax') / 100)) / (1 - ($interest / 100)));
						
						/* Valor por parcela */
						$installment_value = $installment_total/$key;
						
						$installments[$key] = array(
							'installment' => $key,
							'value' => $installment_value,
							'text_value' => $this->currency->format($installment_value, $this->session->data['currency']),
							'total' => $installment_total,
							'text_total' => $this->currency->format($installment_total, $this->session->data['currency'])
						);
					} else {
						$installments[$key] = array(
							'installment' => $key,
							'value' => $this->getOrderTotal($this->session->data['order_id']),
							'text' => $this->currency->format($this->getOrderTotal($this->session->data['order_id']), $this->session->data['currency'])
						);
					}
				}
			}
			
			return $installments;
		} else {
			return false;
		}
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}
	
	public function getOrderTotal($order_id) {
		$this->load->model('checkout/order');
		
		$result = $this->model_checkout_order->getOrder($order_id);
		
		return $result['total'];
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT op.*, p.image FROM " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = op.product_id) WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getInvoiceId($email, $order_id){
		$result = $this->db->query('SELECT oi.* FROM ' . DB_PREFIX . 'order_iugu oi, `' . DB_PREFIX . 'order` o WHERE o.order_id = "' . (int)$order_id . '" AND oi.order_id = "' . (int)$order_id . '" AND MD5(o.email) = "' . $this->db->escape($email) . '"');
		
		return $result->row;
	}
}