<?php
declare(strict_types=1);

namespace Opencart\Catalog\Model\Extension\PurchaseOrder\Payment;

/**
 * Class PurchaseOrder
 *
 * @package Opencart\Catalog\Model\Extension\PurchaseOrder\Payment
 */
class PurchaseOrder extends \Opencart\System\Engine\Model {
	/**
	 * Get available payment methods.
	 *
	 * @param array $address
	 * @return array
	 */
	public function getMethods(array $address = []): array {
		$this->load->language('extension/purchase_order/payment/purchase_order');

		if (!$this->config->get('payment_purchase_order_status')) {
			return [];
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_purchase_order_geo_zone_id') . "' AND `country_id` = '" . (int)($address['country_id'] ?? 0) . "' AND (`zone_id` = '" . (int)($address['zone_id'] ?? 0) . "' OR `zone_id` = '0')");

		$cart_total = (float)$this->cart->getTotal();
		$min_total = (float)$this->config->get('payment_purchase_order_total');

		if ($min_total > 0 && $min_total > $cart_total) {
			$status = false;
		} elseif (!$this->config->get('payment_purchase_order_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		if ($status) {
			if ($this->customer->isLogged()) {
				$customer_group_id = (int)$this->customer->getGroupId();
			} else {
				$customer_group_id = (int)$this->config->get('config_customer_group_id');
			}

			$allowed_groups = (array)$this->config->get('payment_purchase_order_customer_group_id');
			if (!empty($allowed_groups) && !in_array($customer_group_id, array_map('intval', $allowed_groups), true)) {
				$status = false;
			}
		}

		$method_data = [];

		if ($status) {
			$title = $this->language->get('heading_title') ?: $this->language->get('text_title');

			$option_data = [];
			$option_data['purchase_order'] = [
				'code'       => 'purchase_order.purchase_order',
				'name'       => $title,
				'title'      => $title,
				'sort_order' => (int)$this->config->get('payment_purchase_order_sort_order')
			];

			$method_data = [
				'code'       => 'purchase_order.purchase_order',
				'name'       => $title,
				'title'      => $title,
				'option'     => $option_data,
				'sort_order' => (int)$this->config->get('payment_purchase_order_sort_order')
			];
		}

		return $method_data;
	}

	/**
	 * Backward compatibility method for legacy / OpenCart getMethod call.
	 *
	 * @param array $address
	 * @param float $total
	 * @return array
	 */
	public function getMethod(array $address = [], float $total = 0): array {
		return $this->getMethods($address);
	}
}
