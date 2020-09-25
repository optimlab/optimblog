<?php
/**
 * @package    OptimBlog
 * @version    3.0.1.4
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
class ControllerExtensionExtensionInformation extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/extension/information');

		$this->load->model('setting/extension');

		$this->getList();
	}

	public function install() {
		$this->load->language('extension/extension/information');

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->install('information', $this->request->get['extension']);

			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/information/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/information/' . $this->request->get['extension']);

			// Call install method if it exsits
			$this->load->controller('extension/information/' . $this->request->get['extension'] . '/install');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->load->model('setting/event');

		$this->model_setting_event->addEvent('optimblog_admin_language_category', 'admin/language/catalog/category/after', 'extension/extension/information/languageCategory');
		$this->model_setting_event->addEvent('optimblog_admin_language_product', 'admin/language/catalog/product/after', 'extension/extension/information/languageProduct');
		$this->model_setting_event->addEvent('optimblog_admin_language_information', 'admin/language/catalog/information/after', 'extension/extension/information/languageInformation');
		$this->model_setting_event->addEvent('optimblog_admin_language_review', 'admin/language/catalog/review/after', 'extension/extension/information/languageReview');
		$this->model_setting_event->addEvent('optimblog_admin_language_customer_search', 'admin/language/extension/report/customer_search/after', 'extension/extension/information/languageCustomerSearch');

		$this->model_setting_event->addEvent('optimblog_catalog_language_product', 'catalog/language/product/product/after', 'extension/information/optimblog/languageProduct');
		$this->model_setting_event->addEvent('optimblog_catalog_language_information', 'catalog/language/information/information/after', 'extension/information/optimblog/languageInformation');
		$this->model_setting_event->addEvent('optimblog_catalog_language_review', 'catalog/language/mail/review/after', 'extension/information/optimblog/languageReview');

		$this->model_setting_event->addEvent('optimblog_catalog_controller_category_type', 'catalog/controller/product/category/before', 'extension/information/optimblog/controllerCategoryType');

		$this->getList();
	}

	public function uninstall() {
		$this->load->language('extension/extension/information');

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->uninstall('information', $this->request->get['extension']);

			// Call uninstall method if it exsits
			$this->load->controller('extension/information/' . $this->request->get['extension'] . '/uninstall');

			$this->session->data['success'] = $this->language->get('text_success');
		}
		
		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_category');
		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_product');
		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_information');
		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_review');
		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_customer_search');

		$this->model_setting_event->deleteEventByCode('optimblog_catalog_language_product');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_language_information');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_language_review');

		$this->model_setting_event->deleteEventByCode('optimblog_catalog_controller_category_type');

		$this->getList();
	}

	protected function getList() {
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

		$extensions = $this->model_setting_extension->getInstalled('information');

		foreach ($extensions as $key => $value) {
			if (!is_file(DIR_APPLICATION . 'controller/extension/information/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/information/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('information', $value);

				unset($extensions[$key]);
			}
		}

		$this->load->model('setting/store');
		$this->load->model('setting/setting');

		$stores = $this->model_setting_store->getStores();

		$data['extensions'] = array();
		
		// Compatibility code for old extension folders
		$files = glob(DIR_APPLICATION . 'controller/extension/information/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				$this->language->load('extension/information/' . $extension, 'extension');
					
				$store_data = array();
				
				$store_data[] = array(
					'name'   => $this->config->get('config_name'),
					'edit'   => $this->url->link('extension/information/' . $extension, 'user_token=' . $this->session->data['user_token'] . '&store_id=0', true),
					'status' => $this->config->get('information_' . $extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
				);
									
				foreach ($stores as $store) {
					$store_data[] = array(
						'name'   => $store['name'],
						'edit'   => $this->url->link('extension/information/' . $extension, 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store['store_id'], true),
						'status' => $this->model_setting_setting->getSettingValue('information_' . $extension . '_status', $store['store_id']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
					);
				}
				
				$data['extensions'][] = array(
					'name'      => $this->language->get('extension')->get('heading_title'),
					'install'   => $this->url->link('extension/extension/information/install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
					'uninstall' => $this->url->link('extension/extension/information/uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
					'installed' => in_array($extension, $extensions),
					'store'     => $store_data
				);
			}
		}

		$this->response->setOutput($this->load->view('extension/extension/information', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/extension/information')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	// Event
	// admin/language/catalog/category/after
	public function languageCategory(&$route) {
		$this->load->language('extension/information/optimblog_category');
	}

	// admin/language/catalog/product/after
	public function languageProduct(&$route) {
		$this->load->language('extension/information/optimblog_product');
	}

	// admin/language/catalog/information/after
	public function languageInformation(&$route) {
		$this->load->language('extension/information/optimblog_information');
	}

	// admin/language/catalog/review/after
	public function languageReview(&$route) {
		$this->load->language('extension/information/optimblog_review');
	}

	// admin/language/extension/report/customer_search/after
	public function languageCustomerSearch(&$route) {
		$this->load->language('extension/information/optimblog_customer_search');
	}
}