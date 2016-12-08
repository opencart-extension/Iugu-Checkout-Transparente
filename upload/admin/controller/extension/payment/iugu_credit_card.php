<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ControllerExtensionPaymentIuguCreditCard extends Controller {
	public function index() {
		$this->response->redirect($this->url->link('extension/payment/iugu', 'token=' . $this->session->data['token'], 'SSL'));
	}
}