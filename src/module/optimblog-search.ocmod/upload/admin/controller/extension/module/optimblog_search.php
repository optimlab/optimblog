<?php
/**
 * @package    OptimBlog
 * @version    3.1.0.0
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (https://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       https://optimcart.com
 */
class ControllerExtensionModuleOptimBlogSearch extends Controller {
	private $error = array();
	private $version = '3.1.0.0';

	public function index() {
		$this->load->language('extension/module/optimblog_search');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_optimblog_search', $this->request->post);

			// If has been upgraded, verify that the module has the new event registered.
			$this->load->model('setting/event');

			$event = $this->model_setting_event->getEventByCode('optimblog_search');

			if (empty($event)) {
				// Event is missing, add it
				$this->model_setting_event->addEvent('optimblog_search', 'catalog/controller/information/search/before', 'extension/module/optimblog_search/route');
			}

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
			'href' => $this->url->link('extension/module/optimblog_search', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/optimblog_search', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_optimblog_search_status'])) {
			$data['module_optimblog_search_status'] = $this->request->post['module_optimblog_search_status'];
		} else {
			$data['module_optimblog_search_status'] = $this->config->get('module_optimblog_search_status');
		}

		$data['version'] = 'v' . $this->version;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/optimblog_search', $data));
	}

	public function install() {
		$this->load->model('setting/event');

		$this->model_setting_event->addEvent('optimblog_search', 'catalog/controller/information/search/before', 'extension/module/optimblog_search/route');

		$this->model_setting_event->deleteEventByCode('optimblog_search_information');
	}

	public function uninstall() {
		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('optimblog_search');
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/optimblog_search')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}