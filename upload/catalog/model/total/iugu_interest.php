<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ModelTotalIuguInterest extends Model {
	public function getTotal($total) {
        
        return array();
		
		$status = false;
		
		if (isset($this->session->data['payment_method']['code']) && $this->session->data['payment_method']['code'] == 'iugu_credit_card') {
			$settings = $this->config->get('iugu_credit_card_discount');
			
			if ($settings['value'] > 0) {
				$status = true;
			}
		}
		
		if (isset($this->session->data['payment_method']['code']) && $this->session->data['payment_method']['code'] == 'iugu_billet') {
			$settings = $this->config->get('iugu_billet_interest');
			
			if ($settings['value'] > 0) {
				$status = true;
			}
		}
		
		if ($status) {
			$this->load->language('payment/iugu');

			if ($settings['type'] == 'F') {
				$interest = $settings['value'];
			} else {
				$interest = ($this->cart->getSubTotal()/100) * $settings['value'];
			}

			if ($interest > 0) {
				$total['totals'][] = array(
					'code'       => 'iugu_interest',
					'title'      => $this->language->get('text_increate'),
					'value'      => +$interest,
					'sort_order' => ($this->config->get('sub_total_sort_order') + 1)
				);

				//$total['total'] += $interest;
			}
		}
	}
}