<?php
declare(strict_types=1);

namespace Opencart\Admin\Controller\Extension\PurchaseOrder\Payment;

/**
 * Class PurchaseOrder
 *
 * @package Opencart\Admin\Controller\Extension\PurchaseOrder\Payment
 */
class PurchaseOrder extends \Opencart\System\Engine\Controller {
	private array $error = [];

	/**
	 * Render the extension configuration page.
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('extension/purchase_order/payment/purchase_order');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purchase_order/payment/purchase_order', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/purchase_order/payment/purchase_order.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$config_fields = [
			'payment_purchase_order_total',
			'payment_purchase_order_required',
			'payment_purchase_order_order_status_id',
			'payment_purchase_order_geo_zone_id',
			'payment_purchase_order_status',
			'payment_purchase_order_sort_order'
		];

		foreach ($config_fields as $field) {
			$data[$field] = $this->config->get($field);
		}

		$customer_group_id = $this->config->get('payment_purchase_order_customer_group_id');
		$data['payment_purchase_order_customer_group_id'] = is_array($customer_group_id) ? $customer_group_id : [];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/purchase_order/payment/purchase_order', $data));
	}

	/**
	 * Save extension settings.
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/purchase_order/payment/purchase_order');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/purchase_order/payment/purchase_order')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('payment_purchase_order', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Install extension handler.
	 *
	 * @return void
	 */
	public function install(): void {
		if ($this->user->hasPermission('modify', 'extension/purchase_order/payment/purchase_order')) {
			$this->load->model('setting/setting');

			$defaults = [
				'payment_purchase_order_status' => 0,
				'payment_purchase_order_sort_order' => 0,
				'payment_purchase_order_required' => 0,
				'payment_purchase_order_total' => 0.00
			];

			$this->model_setting_setting->editSetting('payment_purchase_order', $defaults);
		}
	}

	/**
	 * Uninstall extension handler.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		if ($this->user->hasPermission('modify', 'extension/purchase_order/payment/purchase_order')) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->deleteSetting('payment_purchase_order');
		}
	}
}
