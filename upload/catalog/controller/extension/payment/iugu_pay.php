<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ControllerPaymentIuguPay extends Controller {
	
	public function index() {
		
		if (isset($this->request->get['order_id'])) {
			preg_match('/(.*)-(.*)-(.*)/', $this->request->get['order_id'], $teste);
			
			$email = isset($teste[1]) ? $teste[1] : '';
			$order_id = isset($teste[2]) ? $teste[2] : 0;
			
			$this->load->model('payment/iugu');
			
			$invoice = $this->model_payment_iugu->getInvoiceId($email, $order_id);
			
			$data['invoice'] = str_replace('.pdf', '', $invoice['pdf']);
			
			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('payment/iugu_pay.tpl', $data));
		} else {
			$this->response->redirect($this->url->link('error/not_found'));
		}
	}
}