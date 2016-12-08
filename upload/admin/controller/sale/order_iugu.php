<?php
/*
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
*/
class ControllerSaleOrderIugu extends Controller {
	
	public function sendMail() {
		$this->language->load('order/iugu');
		
		$file = $this->request->post['pdf'];
		
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
		$mail->setTo(urldecode($this->request->post['email']));
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject(sprintf($this->language->get('text_mail_subject'), $this->config->get('config_name')));
		$mail->setHtml($this->language->get('text_mail_html'));
		$mail->setText($this->language->get('text_mail_text'));
		$mail->addAttachment($file);
		$mail->send();
	}
	
	public function refund() {
		if ($this->user->hasPermission('modify', 'sale/order_iugu')) {
			
			$this->language->load('sale/order_iugu');
			
			$this->load->library('Iugu');
		
			Iugu::setApiKey($this->config->get('iugu_token'));
			$invoice = Iugu_Invoice::fetch($this->request->post['invoice_id']);
			$result = $invoice->refund();
		
			echo json_encode(array(
				'success' => is_string($result) ? false : true,
				'message' => is_string($result) ? $result : $this->language->get('text_invoice_refunded')
			));
		}
	}
	
	public function cancel() {
		if ($this->user->hasPermission('modify', 'sale/order_iugu')) {
			
            $this->language->load('sale/order_iugu');
            
            $this->load->library('Iugu');
            
            $error = false;
            $result = null;
            
            try {
                Iugu::setApiKey($this->config->get('iugu_token'));            
                $invoice = Iugu_Invoice::fetch($this->request->post["invoice_id"]);
                
                if ($invoice instanceof Iugu_Invoice) {
                    $result = $invoice->cancel();
                    $error = true;
                }
            } catch (Exception $e) {}
		
            $message = is_string($result) ? $result : $this->language->get('text_invoice_canceled');
        
			echo json_encode(array(
				'success' => $error,
				'message' => $error ? $message : "Error"
			));
		}
	}
}