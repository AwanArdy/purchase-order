<?php
//==============================================
class ControllerExtensionPaymentPurchaseOrder extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('extension/payment/purchase_order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = 0;
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_purchase_order', $this->request->post);				

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->get['continue'])){
				$this->response->redirect($this->url->link('extension/payment/purchase_order', 'user_token=' . $this->session->data['user_token'], true));
			} else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
			}
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$setting = $this->model_setting_setting->getSetting('payment_purchase_order', $store_id);
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/purchase_order', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/purchase_order', 'user_token=' . $this->session->data['user_token'], true);
		$data['continue'] = $this->url->link('extension/payment/purchase_order', 'user_token=' . $this->session->data['user_token'] . '&continue=1', true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
		
		$config_data = array(
			'payment_purchase_order_total',
			'payment_purchase_order_required',
			'payment_purchase_order_order_status_id',
			'payment_purchase_order_geo_zone_id',
			'payment_purchase_order_status',
			'payment_purchase_order_sort_order'
        );
		
		foreach ($config_data as $conf) {
            if (isset($this->request->post[$conf])) {
                $data[$conf] = $this->request->post[$conf];
            } else {
                $data[$conf] = $this->config->get($conf);
            }
        }

		if (isset($this->request->post['payment_purchase_order_customer_group_id'])){
			$data['payment_purchase_order_customer_group_id'] = $this->request->post['payment_purchase_order_customer_group_id'];
		} elseif (isset($setting['payment_purchase_order_customer_group_id'])) {
			$data['payment_purchase_order_customer_group_id'] = $setting['payment_purchase_order_customer_group_id'];
		} else {
			$data['payment_purchase_order_customer_group_id'] = array();
		}
			
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/purchase_order', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/purchase_order')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
?>