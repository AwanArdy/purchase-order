<?php
declare(strict_types=1);

namespace Opencart\Catalog\Controller\Extension\PurchaseOrder\Payment;

/**
 * Class PurchaseOrder
 *
 * @package Opencart\Catalog\Controller\Extension\PurchaseOrder\Payment
 */
class PurchaseOrder extends \Opencart\System\Engine\Controller {
	/**
	 * Index method to output payment form.
	 *
	 * @return string
	 */
	public function index(): string {
		$this->load->language('extension/purchase_order/payment/purchase_order');

		$data['required'] = (bool)$this->config->get('payment_purchase_order_required');
		$data['language'] = $this->config->get('config_language');
		$data['button_confirm'] = $this->language->get('button_confirm') ?: 'Confirm Order';

		return $this->load->view('extension/purchase_order/payment/purchase_order', $data);
	}

	/**
	 * Confirm payment method call.
	 *
	 * @return void
	 */
	public function confirm(): void {
		$this->load->language('extension/purchase_order/payment/purchase_order');

		$json = [];

		$po_number = trim($this->request->post['po_number'] ?? '');
		$required = (bool)$this->config->get('payment_purchase_order_required');

		if ($required && $po_number === '') {
			$json['error']['po_number'] = $this->language->get('error_blank_po_number');
		} elseif ($po_number !== '' && !preg_match('/^[0-9a-zA-Z\-\/\_\.\#\:\s]+$/', $po_number)) {
			$json['error']['po_number'] = $this->language->get('error_invalid_po_number');
		}

		if (!isset($json['error'])) {
			$this->load->model('checkout/order');

			$order_id = (int)($this->session->data['order_id'] ?? 0);
			$po_title = $this->language->get('text_title') . ($po_number !== '' ? ' (#' . $po_number . ')' : '');

			// Update session payment method if present
			if (isset($this->session->data['payment_method']) && is_array($this->session->data['payment_method'])) {
				$this->session->data['payment_method']['name'] = $po_title;
				$this->session->data['payment_method']['title'] = $po_title;
			}

			// Format payment_method as JSON for OpenCart 4 compatibility
			$payment_method_data = [
				'name' => $po_title,
				'code' => 'purchase_order.purchase_order'
			];

			$payment_method_json = json_encode($payment_method_data);

			if ($order_id) {
				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_method` = '" . $this->db->escape($payment_method_json) . "' WHERE `order_id` = '" . $order_id . "'");
			}

			$comment = $po_number !== '' ? $this->language->get('entry_po_number') . ': ' . $po_number : '';
			$order_status_id = (int)$this->config->get('payment_purchase_order_order_status_id');

			$this->model_checkout_order->addHistory($order_id, $order_status_id, $comment, true);

			$json['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
