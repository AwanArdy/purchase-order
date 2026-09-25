<?php
class ControllerExtensionPaymentPurchaseOrder extends Controller {
	public function index() {
		$this->language->load('extension/payment/purchase_order');

		if ($this->config->get('payment_purchase_order_required') == '1') {
			$data['required'] = true;
		} else {
			$data['required'] = false;
		}
		
		$data['continue'] = $this->url->link('checkout/success');

		return $this->load->view('extension/payment/purchase_order', $data);
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'purchase_order') {
			$this->language->load('extension/payment/purchase_order');

			$this->load->model('checkout/order');
			
			$comment  = ($this->language->get('entry_po_number') . ' ' . $this->request->post['po_number']);
			
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_method` = '" . $this->language->get('text_title') . " (#" . $this->db->escape($this->request->post['po_number']) . ")' WHERE `order_id` = '" . $this->session->data['order_id'] . "'");
		
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_purchase_order_order_status_id'), $comment, true);
		}
	}
}
?>