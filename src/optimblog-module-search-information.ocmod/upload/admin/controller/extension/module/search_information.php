<?php
/**
 * @package    OptimBlog
 * @version    3.0.1.0
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
class ControllerExtensionModuleSearchInformation extends Controller {
	private $error = array();

	public function index() {
		// Version
		define('OPTIMBLOGSEARCHINFORMATION', '3.0.1.0');

		$data['version'] = 'v' . OPTIMBLOGSEARCHINFORMATION;

		$this->load->language('extension/module/search_information');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_search_information', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/search_information', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/search_information', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_search_information_status'])) {
			$data['module_search_information_status'] = $this->request->post['module_search_information_status'];
		} else {
			$data['module_search_information_status'] = $this->config->get('module_search_information_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/search_information', $data));
	}

	public function install() {
		$this->load->model('setting/event');

		$this->model_setting_event->addEvent('optimblog_search_information', 'catalog/controller/information/search/before', 'extension/module/search_information/route');
	}

	public function uninstall() {
		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('optimblog_search_information');
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/search_information')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}