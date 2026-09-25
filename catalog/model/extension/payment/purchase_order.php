<?php
class ModelExtensionPaymentPurchaseOrder extends Model {
	public function getMethod($address, $total) {
		$this->language->load('extension/payment/purchase_order');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_purchase_order_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('payment_purchase_order_total') > 0 && $this->config->get('payment_purchase_order_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_purchase_order_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getGroupId(); 
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id'); 
		}
		
		if ($this->config->get('payment_purchase_order_customer_group_id') == null){
			$status = true;
		} elseif (in_array($customer_group_id, $this->config->get('payment_purchase_order_customer_group_id'))) {
			$status = true;
		} else {
			$status = false;
		}
			
		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'purchase_order',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_purchase_order_sort_order')
			);
		}

		return $method_data;
	}
}
?>