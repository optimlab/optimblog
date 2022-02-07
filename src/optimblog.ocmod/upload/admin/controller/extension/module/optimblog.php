<?php
/**
 * @package    OptimBlog
 * @version    3.1.0.1
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (https://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       https://optimcart.com
 */
class ControllerExtensionModuleOptimBlog extends Controller {
	private $error = array();
	private $version = '3.1.0.1';
	private $github = 'https://api.github.com/repos/optimlab/optimblog';
	private $releases = '/releases';
	private $latest = '/latest';

	public function index() {
		$this->load->language('extension/module/optimblog');

		$this->load->model('setting/setting');

		$this->getList();
	}

	public function edit() {
		$this->load->language('extension/module/optimblog');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->request->post['module_optimblog_version'] = $this->version;

			$this->model_setting_setting->editSetting('module_optimblog', $this->request->post, $this->request->get['store_id']);
            
			if (isset($this->request->post['module_optimblog_information_script']['footer'])) {
				// If has been upgraded, verify that the module has the new event registered.
				$this->load->model('setting/event');

				$event = $this->model_setting_event->getEventByCode('optimblog_catalog_view_footer');

				if (empty($event)) {
					// Event is missing, add it
					$this->model_setting_event->addEvent('optimblog_catalog_view_footer', 'catalog/view/common/footer/before', 'extension/module/optimblog/viewFooterBefore');
				}
			} else {
				$this->load->model('setting/event');

				$event = $this->model_setting_event->getEventByCode('optimblog_catalog_view_footer');

				if (!empty($event)) {
					$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_footer');
				}
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/optimblog', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getForm();
	}

	protected function getList() {
		$this->document->setTitle($this->language->get('heading_title'));

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
			'href' => $this->url->link('extension/module/optimblog', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['user_token'] = $this->session->data['user_token'];

		$data['extension'] = $this->url->link('extension/module/optimblog/extension', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$this->load->model('setting/store');

		$stores = $this->model_setting_store->getStores();

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'name'   => $this->config->get('config_name'),
			'edit'   => $this->url->link('extension/module/optimblog/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=0', true),
			'status' => $this->model_setting_setting->getSettingValue('module_optimblog_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
		);

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'name'   => $store['name'],
				'edit'   => $this->url->link('extension/module/optimblog/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store['store_id'], true),
				'status' => $this->model_setting_setting->getSettingValue('module_optimblog_status', $store['store_id']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
			);
		}

		$data['version'] = 'v' . $this->version;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/optimblog_list', $data));
	}

	protected function getForm() {
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];

			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['description_length'])) {
			$data['error_description_length'] = $this->error['description_length'];
		} else {
			$data['error_description_length'] = '';
		}

		if (isset($this->error['information_limit'])) {
			$data['error_information_limit'] = $this->error['information_limit'];
		} else {
			$data['error_information_limit'] = '';
		}

		if (isset($this->error['image_category'])) {
			$data['error_image_category'] = $this->error['image_category'];
		} else {
			$data['error_image_category'] = '';
		}

		if (isset($this->error['image_thumb'])) {
			$data['error_image_thumb'] = $this->error['image_thumb'];
		} else {
			$data['error_image_thumb'] = '';
		}

		if (isset($this->error['image_popup'])) {
			$data['error_image_popup'] = $this->error['image_popup'];
		} else {
			$data['error_image_popup'] = '';
		}

		if (isset($this->error['image_information'])) {
			$data['error_image_information'] = $this->error['image_information'];
		} else {
			$data['error_image_information'] = '';
		}

		if (isset($this->error['image_additional'])) {
			$data['error_image_additional'] = $this->error['image_additional'];
		} else {
			$data['error_image_additional'] = '';
		}

		if (isset($this->error['image_related'])) {
			$data['error_image_related'] = $this->error['image_related'];
		} else {
			$data['error_image_related'] = '';
		}

		if (isset($this->error['image_category_popup'])) {
			$data['error_image_category_popup'] = $this->error['image_category_popup'];
		} else {
			$data['error_image_category_popup'] = '';
		}

		if (isset($this->error['image_category_additional'])) {
			$data['error_image_category_additional'] = $this->error['image_category_additional'];
		} else {
			$data['error_image_category_additional'] = '';
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
			'href' => $this->url->link('extension/module/optimblog', 'user_token=' . $this->session->data['user_token'], true)
		);

		if ($this->request->get['store_id']) {
			$this->load->model('setting/store');

			$store = $this->model_setting_store->getStore($this->request->get['store_id']);

			$data['store_name'] = $store['name'];
		} else {
			$data['store_name'] = $this->config->get('config_name');
		}

		$data['breadcrumbs'][] = array(
			'text' => $data['store_name'],
			'href' => $this->url->link('extension/module/optimblog/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true)
		);

		$data['action'] = $this->url->link('extension/module/optimblog/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);

		$data['cancel'] = $this->url->link('extension/module/optimblog', 'user_token=' . $this->session->data['user_token'], true);

		$data['download'] = $this->url->link('extension/module/optimblog/export', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);

		$data['theme'] = $this->url->link('extension/module/optimblog/theme', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);

		$data['user_token'] = $this->session->data['user_token'];

		$data['store_id'] = $this->request->get['store_id'];

		$data['config_theme'] = $this->model_setting_setting->getSettingValue('config_theme', $this->request->get['store_id']);

		$setting_info = $this->model_setting_setting->getSetting('theme_' . $data['config_theme'], $this->request->get['store_id']);

		$this->load->language('extension/theme/' . $data['config_theme'], 'theme');

		$data['theme_title'] = $this->language->get('theme')->get('heading_title');

		$data['theme_directory'] = $setting_info['theme_' . $data['config_theme'] .'_directory'];

//		$data['directories'] = array();
//
//		$directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);
//
//		foreach ($directories as $directory) {
//			$data['directories'][] = basename($directory);
//		}

		if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$setting_info = $this->model_setting_setting->getSetting('module_optimblog', $this->request->get['store_id']);
		}
		
		if (isset($this->request->post['module_optimblog_status'])) {
			$data['module_optimblog_status'] = $this->request->post['module_optimblog_status'];
		} elseif (isset($setting_info['module_optimblog_status'])) {
			$data['module_optimblog_status'] = $setting_info['module_optimblog_status'];
		} else {
			$data['module_optimblog_status'] = '';
		}
		
		if (isset($this->request->post['module_optimblog_canonical_category_product'])) {
			$data['module_optimblog_canonical_category_product'] = $this->request->post['module_optimblog_canonical_category_product'];
		} elseif (isset($setting_info['module_optimblog_canonical_category_product'])) {
			$data['module_optimblog_canonical_category_product'] = $setting_info['module_optimblog_canonical_category_product'];
		} else {
			$data['module_optimblog_canonical_category_product'] = '';
		}
		
		if (isset($this->request->post['module_optimblog_canonical_category_information'])) {
			$data['module_optimblog_canonical_category_information'] = $this->request->post['module_optimblog_canonical_category_information'];
		} elseif (isset($setting_info['module_optimblog_canonical_category_information'])) {
			$data['module_optimblog_canonical_category_information'] = $setting_info['module_optimblog_canonical_category_information'];
		} else {
			$data['module_optimblog_canonical_category_information'] = '';
		}
		
		if (isset($this->request->post['module_optimblog_canonical_information'])) {
			$data['module_optimblog_canonical_information'] = $this->request->post['module_optimblog_canonical_information'];
		} elseif (isset($setting_info['module_optimblog_canonical_information'])) {
			$data['module_optimblog_canonical_information'] = $setting_info['module_optimblog_canonical_information'];
		} else {
			$data['module_optimblog_canonical_information'] = '';
		}
		
		if (isset($this->request->post['module_optimblog_breadcrumbs_category_product'])) {
			$data['module_optimblog_breadcrumbs_category_product'] = $this->request->post['module_optimblog_breadcrumbs_category_product'];
		} elseif (isset($setting_info['module_optimblog_breadcrumbs_category_product'])) {
			$data['module_optimblog_breadcrumbs_category_product'] = $setting_info['module_optimblog_breadcrumbs_category_product'];
		} else {
			$data['module_optimblog_breadcrumbs_category_product'] = 1;
		}
		
		if (isset($this->request->post['module_optimblog_breadcrumbs_category_information'])) {
			$data['module_optimblog_breadcrumbs_category_information'] = $this->request->post['module_optimblog_breadcrumbs_category_information'];
		} elseif (isset($setting_info['module_optimblog_breadcrumbs_category_information'])) {
			$data['module_optimblog_breadcrumbs_category_information'] = $setting_info['module_optimblog_breadcrumbs_category_information'];
		} else {
			$data['module_optimblog_breadcrumbs_category_information'] = 1;
		}
		
		if (isset($this->request->post['module_optimblog_breadcrumbs_product'])) {
			$data['module_optimblog_breadcrumbs_product'] = $this->request->post['module_optimblog_breadcrumbs_product'];
		} elseif (isset($setting_info['module_optimblog_breadcrumbs_product'])) {
			$data['module_optimblog_breadcrumbs_product'] = $setting_info['module_optimblog_breadcrumbs_product'];
		} else {
			$data['module_optimblog_breadcrumbs_product'] = 1;
		}
		
		if (isset($this->request->post['module_optimblog_breadcrumbs_information'])) {
			$data['module_optimblog_breadcrumbs_information'] = $this->request->post['module_optimblog_breadcrumbs_information'];
		} elseif (isset($setting_info['module_optimblog_breadcrumbs_information'])) {
			$data['module_optimblog_breadcrumbs_information'] = $setting_info['module_optimblog_breadcrumbs_information'];
		} else {
			$data['module_optimblog_breadcrumbs_information'] = 1;
		}
		
		if (isset($this->request->post['module_optimblog_information_author'])) {
			$data['module_optimblog_information_author'] = $this->request->post['module_optimblog_information_author'];
		} elseif (isset($setting_info['module_optimblog_information_author'])) {
			$data['module_optimblog_information_author'] = $setting_info['module_optimblog_information_author'];
		} else {
			$data['module_optimblog_information_author'] = '';
		}

		if (isset($this->request->post['module_optimblog_information_date'])) {
			$data['module_optimblog_information_date'] = $this->request->post['module_optimblog_information_date'];
		} elseif (isset($setting_info['module_optimblog_information_date'])) {
			$data['module_optimblog_information_date'] = $setting_info['module_optimblog_information_date'];
		} else {
			$data['module_optimblog_information_date'] = '';
		}

		if (isset($this->request->post['module_optimblog_information_manufacturer'])) {
			$data['module_optimblog_information_manufacturer'] = $this->request->post['module_optimblog_information_manufacturer'];
		} elseif (isset($setting_info['module_optimblog_information_manufacturer'])) {
			$data['module_optimblog_information_manufacturer'] = $setting_info['module_optimblog_information_manufacturer'];
		} else {
			$data['module_optimblog_information_manufacturer'] = '';
		}

		if (isset($this->request->post['module_optimblog_information_review'])) {
			$data['module_optimblog_information_review'] = $this->request->post['module_optimblog_information_review'];
		} elseif (isset($setting_info['module_optimblog_information_review'])) {
			$data['module_optimblog_information_review'] = $setting_info['module_optimblog_information_review'];
		} else {
			$data['module_optimblog_information_review'] = '';
		}

		if (isset($this->request->post['module_optimblog_review_status'])) {
			$data['module_optimblog_review_status'] = $this->request->post['module_optimblog_review_status'];
		} elseif (isset($setting_info['module_optimblog_review_status'])) {
			$data['module_optimblog_review_status'] = $setting_info['module_optimblog_review_status'];
		} else {
			$data['module_optimblog_review_status'] = '';
		}

		if (isset($this->request->post['module_optimblog_review_guest'])) {
			$data['module_optimblog_review_guest'] = $this->request->post['module_optimblog_review_guest'];
		} elseif (isset($setting_info['module_optimblog_review_guest'])) {
			$data['module_optimblog_review_guest'] = $setting_info['module_optimblog_review_guest'];
		} else {
			$data['module_optimblog_review_guest'] = '';
		}

		if (isset($this->request->post['module_optimblog_captcha'])) {
			$data['module_optimblog_captcha'] = $this->request->post['module_optimblog_captcha'];
		} elseif (isset($setting_info['module_optimblog_captcha'])) {
			$data['module_optimblog_captcha'] = $setting_info['module_optimblog_captcha'];
		} else {
			$data['module_optimblog_captcha'] = '';
		}
		
		$this->load->model('setting/extension');

		$data['captchas'] = array();

		// Get a list of installed captchas
		$extensions = $this->model_setting_extension->getInstalled('captcha');

		foreach ($extensions as $code) {
			$this->language->load('extension/captcha/' . $code, 'extension');

			if ($this->config->get('captcha_' . $code . '_status')) {
				$data['captchas'][] = array(
					'text'  => $this->language->get('extension')->get('heading_title'),
					'value' => $code
				);
			}
		}
        
		if (isset($this->request->post['module_optimblog_information_show'])) {
			$data['module_optimblog_information_show'] = $this->request->post['module_optimblog_information_show'];
		} elseif (isset($setting_info['module_optimblog_information_show'])) {
			$data['module_optimblog_information_show'] = $setting_info['module_optimblog_information_show'];
		} else {
			$data['module_optimblog_information_show'] = 1;
		}		

		if (isset($this->request->post['module_optimblog_information_description_length'])) {
			$data['module_optimblog_information_description_length'] = $this->request->post['module_optimblog_information_description_length'];
		} elseif (isset($setting_info['module_optimblog_information_description_length'])) {
			$data['module_optimblog_information_description_length'] = $setting_info['module_optimblog_information_description_length'];
		} else {
			$data['module_optimblog_information_description_length'] = 100;
		}
		
		if (isset($this->request->post['module_optimblog_information_count'])) {
			$data['module_optimblog_information_count'] = $this->request->post['module_optimblog_information_count'];
		} elseif (isset($setting_info['module_optimblog_information_count'])) {
			$data['module_optimblog_information_count'] = $setting_info['module_optimblog_information_count'];
		} else {
			$data['module_optimblog_information_count'] = '';
		}

		if (isset($this->request->post['module_optimblog_share'])) {
			$data['module_optimblog_share'] = $this->request->post['module_optimblog_share'];
		} elseif (isset($setting_info['module_optimblog_share'])) {
			$data['module_optimblog_share'] = $setting_info['module_optimblog_share'];
		} else {
			$data['module_optimblog_share']  = '';
		}
		
		if (isset($this->request->post['module_optimblog_information_thumb'])) {
			$data['module_optimblog_information_thumb'] = $this->request->post['module_optimblog_information_thumb'];
		} elseif (isset($setting_info['module_optimblog_information_thumb'])) {
			$data['module_optimblog_information_thumb'] = $setting_info['module_optimblog_information_thumb'];
		} else {
			$data['module_optimblog_information_thumb']  = '';
		}
		
		if (isset($this->request->post['module_optimblog_information_style'])) {
			$data['module_optimblog_information_style'] = $this->request->post['module_optimblog_information_style'];
		} elseif (isset($setting_info['module_optimblog_information_style'])) {
			$data['module_optimblog_information_style'] = $setting_info['module_optimblog_information_style'];
		} else {
			$data['module_optimblog_information_style'] = array(
				'catalog/view/javascript/jquery/magnific/magnific-popup.css',
				'catalog/view/javascript/jquery/swiper/css/swiper.min.css',
				'catalog/view/javascript/jquery/swiper/css/opencart.css'
			);
		}

		if (isset($this->request->post['module_optimblog_information_script'])) {
			$data['module_optimblog_information_script'] = $this->request->post['module_optimblog_information_script'];
		} elseif (isset($setting_info['module_optimblog_information_script'])) {
			$data['module_optimblog_information_script'] = $setting_info['module_optimblog_information_script'];
		} else {
			$data['module_optimblog_information_script']['header'] = array(
				'catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js',
				'catalog/view/javascript/jquery/swiper/js/swiper.jquery.js'
			);
		}

		if (isset($this->request->post['module_optimblog_category_author'])) {
			$data['module_optimblog_category_author'] = $this->request->post['module_optimblog_category_author'];
		} elseif (isset($setting_info['module_optimblog_category_author'])) {
			$data['module_optimblog_category_author'] = $setting_info['module_optimblog_category_author'];
		} else {
			$data['module_optimblog_category_author'] = '';
		}

		if (isset($this->request->post['module_optimblog_category_date'])) {
			$data['module_optimblog_category_date'] = $this->request->post['module_optimblog_category_date'];
		} elseif (isset($setting_info['module_optimblog_category_date'])) {
			$data['module_optimblog_category_date'] = $setting_info['module_optimblog_category_date'];
		} else {
			$data['module_optimblog_category_date'] = '';
		}

		if (isset($this->request->post['module_optimblog_category_review'])) {
			$data['module_optimblog_category_review'] = $this->request->post['module_optimblog_category_review'];
		} elseif (isset($setting_info['module_optimblog_category_review'])) {
			$data['module_optimblog_category_review'] = $setting_info['module_optimblog_category_review'];
		} else {
			$data['module_optimblog_category_review'] = '';
		}

		if (isset($this->request->post['module_optimblog_category_view'])) {
			$data['module_optimblog_category_view'] = $this->request->post['module_optimblog_category_view'];
		} elseif (isset($setting_info['module_optimblog_category_view'])) {
			$data['module_optimblog_category_view'] = $setting_info['module_optimblog_category_view'];
		} else {
			$data['module_optimblog_category_view'] = 'list';
		}		
		
		if (isset($this->request->post['module_optimblog_category_view_show'])) {
			$data['module_optimblog_category_view_show'] = $this->request->post['module_optimblog_category_view_show'];
		} elseif (isset($setting_info['module_optimblog_category_view_show'])) {
			$data['module_optimblog_category_view_show'] = $setting_info['module_optimblog_category_view_show'];
		} else {
			$data['module_optimblog_category_view_show'] = '';
		}		
		
		if (isset($this->request->post['module_optimblog_information_limit'])) {
			$data['module_optimblog_information_limit'] = $this->request->post['module_optimblog_information_limit'];
		} elseif (isset($setting_info['module_optimblog_information_limit'])) {
			$data['module_optimblog_information_limit'] = $setting_info['module_optimblog_information_limit'];
		} else {
			$data['module_optimblog_information_limit'] = 15;
		}		
		
		if (isset($this->request->post['module_optimblog_category_limit_show'])) {
			$data['module_optimblog_category_limit_show'] = $this->request->post['module_optimblog_category_limit_show'];
		} elseif (isset($setting_info['module_optimblog_category_limit_show'])) {
			$data['module_optimblog_category_limit_show'] = $setting_info['module_optimblog_category_limit_show'];
		} else {
			$data['module_optimblog_category_limit_show'] = '';
		}		
		
		$data['sorts'] = array();

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'i.sort_order-ASC'
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_name_asc'),
			'value' => 'id.title-ASC'
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_name_desc'),
			'value' => 'id.title-DESC'
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_date_asc'),
			'value' => 'i.date_added-ASC'
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_date_desc'),
			'value' => 'i.date_added-DESC'
		);

		if ($data['module_optimblog_review_status']) {
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_desc'),
				'value' => 'rating-DESC'
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_asc'),
				'value' => 'rating-ASC'
			);
		}

		if (isset($this->request->post['module_optimblog_category_sort'])) {
			$data['module_optimblog_category_sort'] = $this->request->post['module_optimblog_category_sort'];
		} elseif (isset($setting_info['module_optimblog_category_sort'])) {
			$data['module_optimblog_category_sort'] = $setting_info['module_optimblog_category_sort'];
		} else {
			$data['module_optimblog_category_sort'] = 'i.sort_order-ASC';		
		}
		
		if (isset($this->request->post['module_optimblog_category_sort_show'])) {
			$data['module_optimblog_category_sort_show'] = $this->request->post['module_optimblog_category_sort_show'];
		} elseif (isset($setting_info['module_optimblog_category_sort_show'])) {
			$data['module_optimblog_category_sort_show'] = $setting_info['module_optimblog_category_sort_show'];
		} else {
			$data['module_optimblog_category_sort_show'] = '';		
		}
		
		// Exclusion Informations
        $this->load->model('catalog/information');

		if (isset($this->request->post['module_optimblog_exclusion_information'])) {
			$exclusion_informations = $this->request->post['module_optimblog_exclusion_information'];
		} elseif (isset($setting_info['module_optimblog_exclusion_information'])) {
			$exclusion_informations = $setting_info['module_optimblog_exclusion_information'];
		} else {
			$exclusion_informations = array();
		}

		$data['module_optimblog_exclusion_informations'] = array();

		foreach ($exclusion_informations as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['module_optimblog_exclusion_informations'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_information_author'])) {
			$exclusion_informations = $this->request->post['module_optimblog_exclusion_information_author'];
		} elseif (isset($setting_info['module_optimblog_exclusion_information_author'])) {
			$exclusion_informations = $setting_info['module_optimblog_exclusion_information_author'];
		} else {
			$exclusion_informations = array();
		}

		$data['module_optimblog_exclusion_informations_author'] = array();

		foreach ($exclusion_informations as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['module_optimblog_exclusion_informations_author'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_information_date'])) {
			$exclusion_informations = $this->request->post['module_optimblog_exclusion_information_date'];
		} elseif (isset($setting_info['module_optimblog_exclusion_information_date'])) {
			$exclusion_informations = $setting_info['module_optimblog_exclusion_information_date'];
		} else {
			$exclusion_informations = array();
		}

		$data['module_optimblog_exclusion_informations_date'] = array();

		foreach ($exclusion_informations as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['module_optimblog_exclusion_informations_date'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_information_manufacturer'])) {
			$exclusion_informations = $this->request->post['module_optimblog_exclusion_information_manufacturer'];
		} elseif (isset($setting_info['module_optimblog_exclusion_information_manufacturer'])) {
			$exclusion_informations = $setting_info['module_optimblog_exclusion_information_manufacturer'];
		} else {
			$exclusion_informations = array();
		}

		$data['module_optimblog_exclusion_informations_manufacturer'] = array();

		foreach ($exclusion_informations as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['module_optimblog_exclusion_informations_manufacturer'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_information_review'])) {
			$exclusion_informations = $this->request->post['module_optimblog_exclusion_information_review'];
		} elseif (isset($setting_info['module_optimblog_exclusion_information_review'])) {
			$exclusion_informations = $setting_info['module_optimblog_exclusion_information_review'];
		} else {
			$exclusion_informations = array();
		}

		$data['module_optimblog_exclusion_informations_review'] = array();

		foreach ($exclusion_informations as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['module_optimblog_exclusion_informations_review'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		// Exclusion Category
        $this->load->model('catalog/category');

		if (isset($this->request->post['module_optimblog_exclusion_category_author'])) {
			$exclusion_categories = $this->request->post['module_optimblog_exclusion_category_author'];
		} elseif (isset($setting_info['module_optimblog_exclusion_category_author'])) {
			$exclusion_categories = $setting_info['module_optimblog_exclusion_category_author'];
		} else {
			$exclusion_categories = array();
		}

		$data['module_optimblog_exclusion_categories_author'] = array();

		foreach ($exclusion_categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['module_optimblog_exclusion_categories_author'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_category_author_information'])) {
			$exclusion_categories = $this->request->post['module_optimblog_exclusion_category_author_information'];
		} elseif (isset($setting_info['module_optimblog_exclusion_category_author_information'])) {
			$exclusion_categories = $setting_info['module_optimblog_exclusion_category_author_information'];
		} else {
			$exclusion_categories = array();
		}

		$data['module_optimblog_exclusion_categories_author_information'] = array();

		foreach ($exclusion_categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['module_optimblog_exclusion_categories_author_information'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_category_date'])) {
			$exclusion_categories = $this->request->post['module_optimblog_exclusion_category_date'];
		} elseif (isset($setting_info['module_optimblog_exclusion_category_date'])) {
			$exclusion_categories = $setting_info['module_optimblog_exclusion_category_date'];
		} else {
			$exclusion_categories = array();
		}

		$data['module_optimblog_exclusion_categories_date'] = array();

		foreach ($exclusion_categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['module_optimblog_exclusion_categories_date'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_category_date_information'])) {
			$exclusion_categories = $this->request->post['module_optimblog_exclusion_category_date_information'];
		} elseif (isset($setting_info['module_optimblog_exclusion_category_date_information'])) {
			$exclusion_categories = $setting_info['module_optimblog_exclusion_category_date_information'];
		} else {
			$exclusion_categories = array();
		}

		$data['module_optimblog_exclusion_categories_date_information'] = array();

		foreach ($exclusion_categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['module_optimblog_exclusion_categories_date_information'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_category_review'])) {
			$exclusion_categories = $this->request->post['module_optimblog_exclusion_category_review'];
		} elseif (isset($setting_info['module_optimblog_exclusion_category_review'])) {
			$exclusion_categories = $setting_info['module_optimblog_exclusion_category_review'];
		} else {
			$exclusion_categories = array();
		}

		$data['module_optimblog_exclusion_categories_review'] = array();

		foreach ($exclusion_categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['module_optimblog_exclusion_categories_review'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_category_review_information'])) {
			$exclusion_categories = $this->request->post['module_optimblog_exclusion_category_review_information'];
		} elseif (isset($setting_info['module_optimblog_exclusion_category_review_information'])) {
			$exclusion_categories = $setting_info['module_optimblog_exclusion_category_review_information'];
		} else {
			$exclusion_categories = array();
		}

		$data['module_optimblog_exclusion_categories_review_information'] = array();

		foreach ($exclusion_categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['module_optimblog_exclusion_categories_review_information'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_exclusion_category_manufacturer_information'])) {
			$exclusion_categories = $this->request->post['module_optimblog_exclusion_category_manufacturer_information'];
		} elseif (isset($setting_info['module_optimblog_exclusion_category_manufacturer_information'])) {
			$exclusion_categories = $setting_info['module_optimblog_exclusion_category_manufacturer_information'];
		} else {
			$exclusion_categories = array();
		}

		$data['module_optimblog_exclusion_categories_manufacturer_information'] = array();

		foreach ($exclusion_categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['module_optimblog_exclusion_categories_manufacturer_information'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['module_optimblog_image_category_width'])) {
			$data['module_optimblog_image_category_width'] = $this->request->post['module_optimblog_image_category_width'];
		} elseif (isset($setting_info['module_optimblog_image_category_width'])) {
			$data['module_optimblog_image_category_width'] = $setting_info['module_optimblog_image_category_width'];
		} else {
			$data['module_optimblog_image_category_width'] = 80;		
		}
		
		if (isset($this->request->post['module_optimblog_image_category_height'])) {
			$data['module_optimblog_image_category_height'] = $this->request->post['module_optimblog_image_category_height'];
		} elseif (isset($setting_info['module_optimblog_image_category_height'])) {
			$data['module_optimblog_image_category_height'] = $setting_info['module_optimblog_image_category_height'];
		} else {
			$data['module_optimblog_image_category_height'] = 80;
		}
		
		if (isset($this->request->post['module_optimblog_image_thumb_width'])) {
			$data['module_optimblog_image_thumb_width'] = $this->request->post['module_optimblog_image_thumb_width'];
		} elseif (isset($setting_info['module_optimblog_image_thumb_width'])) {
			$data['module_optimblog_image_thumb_width'] = $setting_info['module_optimblog_image_thumb_width'];
		} else {
			$data['module_optimblog_image_thumb_width'] = 840;
		}
		
		if (isset($this->request->post['module_optimblog_image_thumb_height'])) {
			$data['module_optimblog_image_thumb_height'] = $this->request->post['module_optimblog_image_thumb_height'];
		} elseif (isset($setting_info['module_optimblog_image_thumb_height'])) {
			$data['module_optimblog_image_thumb_height'] = $setting_info['module_optimblog_image_thumb_height'];
		} else {
			$data['module_optimblog_image_thumb_height'] = 240;		
		}
		
		if (isset($this->request->post['module_optimblog_image_popup_width'])) {
			$data['module_optimblog_image_popup_width'] = $this->request->post['module_optimblog_image_popup_width'];
		} elseif (isset($setting_info['module_optimblog_image_popup_width'])) {
			$data['module_optimblog_image_popup_width'] = $setting_info['module_optimblog_image_popup_width'];
		} else {
			$data['module_optimblog_image_popup_width'] = 1680;
		}
		
		if (isset($this->request->post['module_optimblog_image_popup_height'])) {
			$data['module_optimblog_image_popup_height'] = $this->request->post['module_optimblog_image_popup_height'];
		} elseif (isset($setting_info['module_optimblog_image_popup_height'])) {
			$data['module_optimblog_image_popup_height'] = $setting_info['module_optimblog_image_popup_height'];
		} else {
			$data['module_optimblog_image_popup_height'] = 480;
		}
		
		if (isset($this->request->post['module_optimblog_image_information_width'])) {
			$data['module_optimblog_image_information_width'] = $this->request->post['module_optimblog_image_information_width'];
		} elseif (isset($setting_info['module_optimblog_image_information_width'])) {
			$data['module_optimblog_image_information_width'] = $setting_info['module_optimblog_image_information_width'];
		} else {
			$data['module_optimblog_image_information_width'] = 228;
		}
		
		if (isset($this->request->post['module_optimblog_image_information_height'])) {
			$data['module_optimblog_image_information_height'] = $this->request->post['module_optimblog_image_information_height'];
		} elseif (isset($setting_info['module_optimblog_image_information_height'])) {
			$data['module_optimblog_image_information_height'] = $setting_info['module_optimblog_image_information_height'];
		} else {
			$data['module_optimblog_image_information_height'] = 228;
		}
		
		if (isset($this->request->post['module_optimblog_image_additional_width'])) {
			$data['module_optimblog_image_additional_width'] = $this->request->post['module_optimblog_image_additional_width'];
		} elseif (isset($setting_info['module_optimblog_image_additional_width'])) {
			$data['module_optimblog_image_additional_width'] = $setting_info['module_optimblog_image_additional_width'];
		} else {
			$data['module_optimblog_image_additional_width'] = 840;
		}
		
		if (isset($this->request->post['module_optimblog_image_additional_height'])) {
			$data['module_optimblog_image_additional_height'] = $this->request->post['module_optimblog_image_additional_height'];
		} elseif (isset($setting_info['module_optimblog_image_additional_height'])) {
			$data['module_optimblog_image_additional_height'] = $setting_info['module_optimblog_image_additional_height'];
		} else {
			$data['module_optimblog_image_additional_height'] = 240;
		}
		
		if (isset($this->request->post['module_optimblog_image_related_width'])) {
			$data['module_optimblog_image_related_width'] = $this->request->post['module_optimblog_image_related_width'];
		} elseif (isset($setting_info['module_optimblog_image_related_width'])) {
			$data['module_optimblog_image_related_width'] = $setting_info['module_optimblog_image_related_width'];
		} else {
			$data['module_optimblog_image_related_width'] = 200;
		}
		
		if (isset($this->request->post['module_optimblog_image_related_height'])) {
			$data['module_optimblog_image_related_height'] = $this->request->post['module_optimblog_image_related_height'];
		} elseif (isset($setting_info['module_optimblog_image_related_height'])) {
			$data['module_optimblog_image_related_height'] = $setting_info['module_optimblog_image_related_height'];
		} else {
			$data['module_optimblog_image_related_height'] = 200;
		}
		
		if (isset($this->request->post['module_optimblog_image_category_popup_width'])) {
			$data['module_optimblog_image_category_popup_width'] = $this->request->post['module_optimblog_image_category_popup_width'];
		} elseif (isset($setting_info['module_optimblog_image_category_popup_width'])) {
			$data['module_optimblog_image_category_popup_width'] = $setting_info['module_optimblog_image_category_popup_width'];
		} else {
			$data['module_optimblog_image_category_popup_width'] = 500;
		}
		
		if (isset($this->request->post['module_optimblog_image_category_popup_height'])) {
			$data['module_optimblog_image_category_popup_height'] = $this->request->post['module_optimblog_image_category_popup_height'];
		} elseif (isset($setting_info['module_optimblog_image_category_popup_height'])) {
			$data['module_optimblog_image_category_popup_height'] = $setting_info['module_optimblog_image_category_popup_height'];
		} else {
			$data['module_optimblog_image_category_popup_height'] = 500;
		}
		
		if (isset($this->request->post['module_optimblog_image_category_additional_width'])) {
			$data['module_optimblog_image_category_additional_width'] = $this->request->post['module_optimblog_image_category_additional_width'];
		} elseif (isset($setting_info['module_optimblog_image_category_additional_width'])) {
			$data['module_optimblog_image_category_additional_width'] = $setting_info['module_optimblog_image_category_additional_width'];
		} else {
			$data['module_optimblog_image_category_additional_width'] = 74;
		}
		
		if (isset($this->request->post['module_optimblog_image_category_additional_height'])) {
			$data['module_optimblog_image_category_additional_height'] = $this->request->post['module_optimblog_image_category_additional_height'];
		} elseif (isset($setting_info['module_optimblog_image_category_additional_height'])) {
			$data['module_optimblog_image_category_additional_height'] = $setting_info['module_optimblog_image_category_additional_height'];
		} else {
			$data['module_optimblog_image_category_additional_height'] = 74;
		}
		
		$data['version'] = 'v' . $this->version;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/optimblog_form', $data));
	}

	public function upgrade() {
		$this->load->language('extension/module/optimblog');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/module/optimblog')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->get['extension'])) {
			$json['error'] = $this->language->get('error_download');
		}

		if (!$json) {
			$curl = curl_init($this->github . '/releases');

			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_USERAGENT, 'OpenCart ' . VERSION);
			curl_setopt($curl, CURLOPT_ENCODING, 'application/vnd.github.v3+json');

			$response = curl_exec($curl);

			$status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

			curl_close($curl);

			$response_info = json_decode($response, true);

			if ($status == 200) {
				foreach ($response_info as $release) {
					if (!$release['prerelease'] && !isset($json['success']) && !isset($json['info'])) {
						foreach ($release['assets'] as $asset) {
							if ($asset['name'] == $this->request->get['extension'] . '.ocmod.zip') {
								if (version_compare($this->version, $release['tag_name'], '>=')) {
									$json['success'] = sprintf($this->language->get('text_version'), $this->version);
								} else {
									$json['info'] = sprintf($this->language->get('text_version_info'), $release['tag_name']);

									$json['url'] = $asset['browser_download_url'];
								}

								break;
							}
						}
					}

				}

				if (!isset($json['success']) && !isset($json['info'])) {
					$json['error'] = $this->language->get('error_download');
				}
			} else {
				$json['error'] = $this->language->get('error_download');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function extension() {
		$this->load->language('extension/module/optimblog');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

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
			'href' => $this->url->link('extension/module/optimblog', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module/optimblog/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['user_token'] = $this->session->data['user_token'];

		$data['cancel'] = $this->url->link('extension/module/optimblog', 'user_token=' . $this->session->data['user_token'], true);

		$language_code = $this->config->get('config_admin_language');

		$data['modules'] = array();

		$modules = array(
			'optimblog_latest',
			'optimblog_featured',
			'optimblog_best',
			'optimblog_category',
			'optimblog_search'
		);

		foreach ($modules as $module) {
			$data['modules'][] = array(
				'module'      => str_replace('_', '-', $module),
				'name'        => $this->language->get($module),
				'description' => $this->language->get($module . '_description'),
				'image'       => 'https://raw.githubusercontent.com/optimlab/optimblog/master/src/module/image/' . $language_code . '/' . str_replace('_', '-', $module) . '.png',
				'href'        => $this->language->get('optimcart_url') . 'extension/optimblog/module/' . str_replace('_', '-', $module),
				'install'     => str_replace('_', '-', $module),
				'installed'   => is_file(DIR_APPLICATION . 'controller/extension/module/' . $module . '.php') ? true : false
			);
		}

		$data['modifications'] = array();

		$modifications = array(
			'optimblog-admin-filter'
		);

		foreach ($modifications as $modification) {
			$data['modifications'][] = array(
				'name'        => $this->language->get($modification),
				'description' => $this->language->get($modification . '_description'),
				'image'       => 'https://raw.githubusercontent.com/optimlab/optimblog/master/src/modification/image/' . $language_code . '/' . $modification . '.png',
				'href'        => $this->language->get('optimcart_url') . 'extension/optimblog/modification/' . $modification,
				'install'     => $modification,
				'installed'   => $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension_install` WHERE `filename` = '" . $this->db->escape($modification) . ".ocmod.zip'")->row ? true : false
			);
		}

		$data['version'] = 'v' . $this->version;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/optimblog_extension', $data));
	}

	public function download() {
		$this->load->language('extension/module/optimblog');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/module/optimblog')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->get['extension'])) {
			$json['error'] = $this->language->get('error_download');
		}

//		if (isset($this->request->get['version'])) {
//			$this->version = $this->request->get['version'];
//		}

		// Check if there is a install zip already there
		$files = glob(DIR_UPLOAD . '*.tmp');

		foreach ($files as $file) {
			if (is_file($file) && (filectime($file) < (time() - 5))) {
				unlink($file);
			}

			if (is_file($file)) {
				$json['error'] = $this->language->get('error_install');

				break;
			}
		}

		// Check for any install directories
		$directories = glob(DIR_UPLOAD . 'tmp-*');

		foreach ($directories as $directory) {
			if (is_dir($directory) && (filectime($directory) < (time() - 5))) {
				// Get a list of files ready to upload
				$files = array();

				$path = array($directory);

				while (count($path) != 0) {
					$next = array_shift($path);

					// We have to use scandir function because glob will not pick up dot files.
					foreach (array_diff(scandir($next), array('.', '..')) as $file) {
						$file = $next . '/' . $file;

						if (is_dir($file)) {
							$path[] = $file;
						}

						$files[] = $file;
					}
				}

				rsort($files);

				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					} elseif (is_dir($file)) {
						rmdir($file);
					}
				}

				rmdir($directory);
			}

			if (is_dir($directory)) {
				$json['error'] = $this->language->get('error_install');

				break;
			}
		}

		if (!$json) {
			$curl = curl_init($this->github . '/releases');

			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_USERAGENT, 'OpenCart ' . VERSION);
			curl_setopt($curl, CURLOPT_ENCODING, 'application/vnd.github.v3+json');

			$response = curl_exec($curl);

			$status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

			curl_close($curl);

			$response_info = json_decode($response, true);

			if ($status == 200) {
				foreach ($response_info as $release) {
					if (!$release['prerelease'] && !isset($json['text'])) {
						foreach ($release['assets'] as $asset) {
							if ($asset['name'] == $this->request->get['extension'] . '.ocmod.zip' && substr(basename($asset['browser_download_url']), -10) == '.ocmod.zip') {
								$this->session->data['install'] = token(10);

								$handle = fopen(DIR_UPLOAD . $this->session->data['install'] . '.tmp', 'w');

								$curl = curl_init($asset['browser_download_url']);

								curl_setopt($curl, CURLOPT_USERAGENT, 'OpenCart ' . VERSION);
								curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
								curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
								curl_setopt($curl, CURLOPT_TIMEOUT, 300);
								curl_setopt($curl, CURLOPT_FILE, $handle);

								curl_exec($curl);

								fclose($handle);

								$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

								curl_close($curl);

								$this->load->model('setting/extension');

								$json['extension_install_id'] = $this->model_setting_extension->addExtensionInstall(basename($asset['browser_download_url']));

								if ($status == 200) {
									$json['text'] = $this->language->get('text_install');

									$json['next'] = str_replace('&amp;', '&', $this->url->link('marketplace/install/install', 'user_token=' . $this->session->data['user_token'] . '&extension_install_id=' . $json['extension_install_id'], true));
								} else {
									$json['error'] = $this->language->get('error_download');

									$json['redirect'] = $asset['browser_download_url'];
								}

								break;
							}
						}
					}
				}
			} else {
				$json['error'] = $this->language->get('error_download');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function theme() {
		$this->load->language('extension/module/optimblog');

		if (!$this->user->hasPermission('modify', 'extension/module/optimblog')) {
			$this->session->data['error'] = $this->language->get('error_permission');

			$this->response->redirect($this->url->link('extension/module/optimblog', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true));
		} else {
			$this->load->model('setting/setting');

			$config_theme = $this->model_setting_setting->getSettingValue('config_theme', $this->request->get['store_id']);

			$setting_info = $this->model_setting_setting->getSetting('theme_' . $config_theme, $this->request->get['store_id']);

			$theme_directory = $setting_info['theme_' . $config_theme .'_directory'];

			$this->load->language('extension/theme/' . $config_theme, 'theme');

			$theme_title = $this->language->get('theme')->get('heading_title');

			$this->session->data['install'] = token(10);

			$handle = fopen(DIR_UPLOAD . $this->session->data['install'] . '.tmp', 'w');

			$curl = curl_init('https://github.com/optimlab/optimblog/releases/download/3.1.0.0/optimblog-theme.ocmod.zip');		

			curl_setopt($curl, CURLOPT_USERAGENT, 'OpenCart ' . VERSION);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 300);
			curl_setopt($curl, CURLOPT_FILE, $handle);

			curl_exec($curl);

			fclose($handle);

			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			curl_close($curl);

			if ($status == 200) {
				$file = DIR_UPLOAD . $this->session->data['install'] . '.tmp';

				// Unzip the files
				$zip = new ZipArchive();

				if ($zip->open($file)) {
					$zip->extractTo(DIR_UPLOAD . 'tmp-' . $this->session->data['install']);
					$zip->close();
				} else {
					$this->session->data['warning'] = $this->language->get('error_unzip');
				}

				// Remove Zip
				unlink($file);

				if (is_dir(DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/upload/catalog/view/theme/theme/')) {
					rename(DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/upload/catalog/view/theme/theme/', DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/upload/catalog/view/theme/' . $theme_directory . '/');

					// Replace setting theme in install.xml
					$file = DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/install.xml';

					$xml = file_get_contents($file);

					if ($xml) {
						$xml = str_replace('theme_directory', $theme_directory, $xml);

						$dom = new DOMDocument('1.0', 'UTF-8');

						if ($dom->loadXML($xml)) {
							$xml = simplexml_import_dom($dom);

							$xml->name = '  OptimBlog - ' . $theme_title;
							$xml->code = 'optimblog-' . $config_theme;
							$xml->version = $this->version;

							$xml->asXML(DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/install.xml');
						}
					}
				}

				if (is_dir(DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/')) {
					$files = array();

					// Get a list of files ready to upload
					$path = array(DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/*');

					while (count($path) != 0) {
						$next = array_shift($path);

						foreach ((array)glob($next) as $file) {
							if (is_dir($file)) {
								$path[] = $file . '/*';
							} elseif (is_file($file)) {
								$files[] = $file;
							}
						}
					}
				}

				$file = DIR_UPLOAD . 'optimblog-' . $config_theme . '-theme.ocmod.zip';
			
				if ($zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
					foreach ($files as $file) {
						$zip->addFile($file, str_replace(DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/', '', $file));                 
					}

					$zip->close();

					$file = DIR_UPLOAD . 'optimblog-' . $config_theme . '-theme.ocmod.zip';
			
					if (is_file($file)) {
						header('Pragma: public');
						header('Expires: 0');
						header('Content-Description: File Transfer');
						header('Content-Type: application/zip');
						header('Content-Disposition: attachment; filename="' . basename($file) . '"');
						header('Content-Length: ' . filesize($file));
						header('Content-Transfer-Encoding: binary');
						readfile($file);
					}

					// Remove Zip and Path-tmp
					unlink($file);

					$this->load->controller('marketplace/install/remove');
				} else {
					$this->session->data['warning'] = $this->language->get('error_file');
				}

				if (isset($this->session->data['warning'])) {
					$this->response->redirect($this->url->link('extension/module/optimblog/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true));
				}
			} else {
				unlink(DIR_UPLOAD . $this->session->data['install'] . '.tmp');

				$this->session->data['warning'] = $this->language->get('error_connection');

				$this->response->redirect($this->url->link('extension/module/optimblog/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true));
			}
		}
	}	

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/optimblog')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['module_optimblog_information_description_length']) {
			$this->error['description_length'] = $this->language->get('error_limit');
		}

		if (!$this->request->post['module_optimblog_information_limit']) {
			$this->error['information_limit'] = $this->language->get('error_limit');
		}

		if (!$this->request->post['module_optimblog_image_category_width'] || !$this->request->post['module_optimblog_image_category_height']) {
			$this->error['image_category'] = $this->language->get('error_image_category');
		}

		if (!$this->request->post['module_optimblog_image_thumb_width'] || !$this->request->post['module_optimblog_image_thumb_height']) {
			$this->error['image_thumb'] = $this->language->get('error_image_thumb');
		}

		if (!$this->request->post['module_optimblog_image_popup_width'] || !$this->request->post['module_optimblog_image_popup_height']) {
			$this->error['image_popup'] = $this->language->get('error_image_popup');
		}

		if (!$this->request->post['module_optimblog_image_information_width'] || !$this->request->post['module_optimblog_image_information_height']) {
			$this->error['image_information'] = $this->language->get('error_image_information');
		}

		if (!$this->request->post['module_optimblog_image_additional_width'] || !$this->request->post['module_optimblog_image_additional_height']) {
			$this->error['image_additional'] = $this->language->get('error_image_additional');
		}

		if (!$this->request->post['module_optimblog_image_related_width'] || !$this->request->post['module_optimblog_image_related_height']) {
			$this->error['image_related'] = $this->language->get('error_image_related');
		}

		if (!$this->request->post['module_optimblog_image_category_popup_width'] || !$this->request->post['module_optimblog_image_category_popup_height']) {
			$this->error['image_category_popup'] = $this->language->get('error_image_category_popup');
		}

		if (!$this->request->post['module_optimblog_image_category_additional_width'] || !$this->request->post['module_optimblog_image_category_additional_height']) {
			$this->error['image_category_additional'] = $this->language->get('error_image_category_additional');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('extension/module/optimblog');
		$this->load->model('setting/event');
        
		$this->model_extension_module_optimblog->createTables();

		$this->model_extension_module_optimblog->update();

		// Language Admin
		$this->model_setting_event->addEvent('optimblog_admin_language_category', 'admin/language/catalog/category/after', 'extension/module/optimblog/languageCategory');
		$this->model_setting_event->addEvent('optimblog_admin_language_product', 'admin/language/catalog/product/after', 'extension/module/optimblog/languageProduct');
		$this->model_setting_event->addEvent('optimblog_admin_language_information', 'admin/language/catalog/information/after', 'extension/module/optimblog/languageInformation');
		$this->model_setting_event->addEvent('optimblog_admin_language_review', 'admin/language/catalog/review/after', 'extension/module/optimblog/languageReview');
		$this->model_setting_event->addEvent('optimblog_admin_language_customer_search', 'admin/language/extension/report/customer_search/after', 'extension/module/optimblog/languageCustomerSearch');

		// Language Catalog
		$this->model_setting_event->addEvent('optimblog_catalog_language_product', 'catalog/language/product/product/after', 'extension/module/optimblog/languageProduct');
		$this->model_setting_event->addEvent('optimblog_catalog_language_information', 'catalog/language/information/information/after', 'extension/module/optimblog/languageInformation');
		$this->model_setting_event->addEvent('optimblog_catalog_language_review', 'catalog/language/mail/review/after', 'extension/module/optimblog/languageReview');

		$this->model_setting_event->addEvent('optimblog_catalog_category_type', 'catalog/controller/product/category/before', 'extension/module/optimblog/controllerCategoryType');
////////////////////////////////////////////////
		$this->model_setting_event->addEvent('optimblog_catalog_view_header', 'catalog/view/common/header/before', 'extension/module/optimblog/viewHeaderBefore');
		$this->model_setting_event->addEvent('optimblog_catalog_view_category', 'catalog/view/product/category/before', 'extension/module/optimblog/viewCategory');
//		$this->model_setting_event->addEvent('optimblog_catalog_view_category_after', 'catalog/view/product/category/after', 'extension/module/optimblog/viewCategoryAfter');
		$this->model_setting_event->addEvent('optimblog_catalog_view_product', 'catalog/view/product/product/before', 'extension/module/optimblog/viewProduct');
		$this->model_setting_event->addEvent('optimblog_catalog_view_information', 'catalog/view/information/information/before', 'extension/module/optimblog/viewInformationBefore');
//		$this->model_setting_event->addEvent('optimblog_catalog_view_information_after', 'catalog/view/information/information/after', 'extension/module/optimblog/viewInformationAfter');

		$this->model_setting_event->addEvent('optimblog_catalog_information_review', 'catalog/controller/information/information/review/before', 'extension/module/optimblog/informationReview');
		$this->model_setting_event->addEvent('optimblog_catalog_information_write', 'catalog/controller/information/information/write/before', 'extension/module/optimblog/informationWrite');
//		$this->model_setting_event->addEvent('optimblog_catalog_information', 'catalog/controller/information/information/before', 'extension/module/optimblog/informationBefore');

		$this->model_setting_event->addEvent('optimblog_catalog_model_information_get', 'catalog/model/catalog/information/getInformation/before', 'extension/module/optimblog/getInformation');
		$this->model_setting_event->addEvent('optimblog_catalog_model_informations_get', 'catalog/model/catalog/information/getInformations/before', 'extension/module/optimblog/getInformations');
//		$this->model_setting_event->addEvent('optimblog_catalog_model_product_get', 'catalog/model/catalog/product/getProduct/before', 'extension/module/optimblog/getProduct');
		$this->model_setting_event->addEvent('optimblog_catalog_model_product_related', 'catalog/model/catalog/product/getProductRelated/before', 'extension/module/optimblog/getProductRelated');
		$this->model_setting_event->addEvent('optimblog_catalog_model_product_review', 'catalog/model/catalog/review/getReviewsByProductId/before', 'extension/module/optimblog/getReviewsByProductId');
		$this->model_setting_event->addEvent('optimblog_catalog_model_product_review_total', 'catalog/model/catalog/review/getTotalReviewsByProductId/before', 'extension/module/optimblog/getTotalReviewsByProductId');
	}

	public function uninstall() {
		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_category');
		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_product');
		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_information');
		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_review');
		$this->model_setting_event->deleteEventByCode('optimblog_admin_language_customer_search');

		$this->model_setting_event->deleteEventByCode('optimblog_catalog_language_product');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_language_information');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_language_review');

		$this->model_setting_event->deleteEventByCode('optimblog_catalog_category_type');
////////////////
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_header');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_footer');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_category');
//		$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_category_after');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_product');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_information');
//		$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_information_after');

		$this->model_setting_event->deleteEventByCode('optimblog_catalog_information_review');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_information_write');
//		$this->model_setting_event->deleteEventByCode('optimblog_catalog_information');

		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_information_get');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_informations_get');
//		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_product_get');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_product_related');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_product_review');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_product_review_total');
	}

	public function export() {
		$this->load->language('extension/module/optimblog');

		if (!$this->user->hasPermission('modify', 'extension/module/optimblog')) {
			$this->session->data['error'] = $this->language->get('error_permission');

			$this->response->redirect($this->url->link('extension/module/optimblog', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true));
		} else {
			if ($this->request->get['store_id']) {
				$this->load->model('setting/store');

				$store = $this->model_setting_store->getStore($this->request->get['store_id']);

				$store_name = $store['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename="optimblog_setting_backup_' . $store_name . '_' . date('Y-m-d_H-i-s', time()) . '.txt"');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->load->model('setting/setting');

			$this->response->setOutput(json_encode($this->model_setting_setting->getSetting('module_optimblog', $this->request->get['store_id'])));
		}
	}	

	public function import() {
		$this->load->language('extension/module/optimblog');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'extension/module/optimblog')) {
			$json['error'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->files['import']['tmp_name']) && is_uploaded_file($this->request->files['import']['tmp_name'])) {
			$filename = tempnam(DIR_UPLOAD, 'bac');
			
			move_uploaded_file($this->request->files['import']['tmp_name'], $filename);
		} elseif (isset($this->request->get['import'])) {
			$filename = html_entity_decode($this->request->get['import'], ENT_QUOTES, 'UTF-8');
		} else {
			$filename = '';
		}
		
		if (!is_file($filename)) {
			$json['error'] = $this->language->get('error_file');
		}	

		// Check to see if any PHP files are trying to be uploaded
		$content = file_get_contents($filename);

		if (preg_match('/\<\?php/i', $content)) {
			$json['error'] = $this->language->get('error_filetype');
		}

		// Return any upload error				
		if ($this->request->files['import']['error'] != UPLOAD_ERR_OK) {
			$json['error'] = $this->language->get('error_upload_' . $this->request->files['import']['error']);
		}

		if (!$json) {
			$handle = fopen($filename, 'r');

			$setting = json_decode(fread($handle, filesize($filename)), true);

			// Check json data
			if ($setting) {
				$this->load->model('setting/setting');

				$this->model_setting_setting->editSetting('module_optimblog', $setting, $this->request->get['store_id']);

				$json['success'] = $this->language->get('text_import');
			} else {
				$json['error'] = $this->language->get('error_datatype');
			}

			fclose($handle);
				
			unlink($filename);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	// Event
	// admin/language/catalog/category/after
	public function languageCategory(&$route) {
		$this->load->language('extension/module/optimblog_admin_category');
	}

	// admin/language/catalog/product/after
	public function languageProduct(&$route) {
		$this->load->language('extension/module/optimblog_admin_product');
	}

	// admin/language/catalog/information/after
	public function languageInformation(&$route) {
		$this->load->language('extension/module/optimblog_admin_information');
	}

	// admin/language/catalog/review/after
	public function languageReview(&$route) {
		$this->load->language('extension/module/optimblog_admin_review');
	}

	// admin/language/extension/report/customer_search/after
	public function languageCustomerSearch(&$route) {
		$this->load->language('extension/module/optimblog_customer_search');
	}
}