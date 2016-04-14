<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ModelSaleOrderIugu extends Model {
	
	public function getOrder($order_id) {
		$result = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'order_iugu WHERE order_id = "' . (int)$order_id . '"');
		
		return $result->row;
	}
}