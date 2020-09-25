<?php
/**
 * @package    OptimBlog
 * @version    3.0.1.4
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
class ControllerExtensionInformationOptimBlog extends Controller {
	private $error = array();

	public function index() {
		// Version
		define('OPTIMBLOG', '3.0.1.4');

		$data['information_optimblog_version'] = OPTIMBLOG;

		$this->load->language('extension/information/optimblog');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('information_optimblog', $this->request->post, $this->request->get['store_id']);
            
			if (isset($this->request->post['information_optimblog_information_script']['footer'])) {
				// If has been upgraded, verify that the module has the new event registered.
				$this->load->model('setting/event');

				$event = $this->model_setting_event->getEventByCode('optimblog_catalog_view_footer');

				if (empty($event)) {
					// Event is missing, add it
					$this->model_setting_event->addEvent('optimblog_catalog_view_footer', 'catalog/view/common/footer/before', 'extension/information/optimblog/viewFooterBefore');
				}
			} else {
				$this->load->model('setting/event');

				$event = $this->model_setting_event->getEventByCode('optimblog_catalog_view_footer');

				if (!empty($event)) {
					$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_footer');
				}
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=information', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
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
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=information', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/information/optimblog', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true)
		);

		$data['action'] = $this->url->link('extension/information/optimblog', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=information', true);

		$data['user_token'] = $this->session->data['user_token'];

		$data['store_id'] = $this->request->get['store_id'];

		$data['download'] = $this->url->link('extension/information/optimblog/export', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);

		if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$setting_info = $this->model_setting_setting->getSetting('information_optimblog', $this->request->get['store_id']);
		}
		
		if (isset($this->request->post['information_optimblog_status'])) {
			$data['information_optimblog_status'] = $this->request->post['information_optimblog_status'];
		} elseif (isset($setting_info['information_optimblog_status'])) {
			$data['information_optimblog_status'] = $setting_info['information_optimblog_status'];
		} else {
			$data['information_optimblog_status'] = '';
		}
		
		if (isset($this->request->post['information_optimblog_canonical_category_product'])) {
			$data['information_optimblog_canonical_category_product'] = $this->request->post['information_optimblog_canonical_category_product'];
		} elseif (isset($setting_info['information_optimblog_canonical_category_product'])) {
			$data['information_optimblog_canonical_category_product'] = $setting_info['information_optimblog_canonical_category_product'];
		} else {
			$data['information_optimblog_canonical_category_product'] = '';
		}
		
		if (isset($this->request->post['information_optimblog_canonical_category_information'])) {
			$data['information_optimblog_canonical_category_information'] = $this->request->post['information_optimblog_canonical_category_information'];
		} elseif (isset($setting_info['information_optimblog_canonical_category_information'])) {
			$data['information_optimblog_canonical_category_information'] = $setting_info['information_optimblog_canonical_category_information'];
		} else {
			$data['information_optimblog_canonical_category_information'] = '';
		}
		
		if (isset($this->request->post['information_optimblog_canonical_information'])) {
			$data['information_optimblog_canonical_information'] = $this->request->post['information_optimblog_canonical_information'];
		} elseif (isset($setting_info['information_optimblog_canonical_information'])) {
			$data['information_optimblog_canonical_information'] = $setting_info['information_optimblog_canonical_information'];
		} else {
			$data['information_optimblog_canonical_information'] = '';
		}
		
		if (isset($this->request->post['information_optimblog_breadcrumbs_category_product'])) {
			$data['information_optimblog_breadcrumbs_category_product'] = $this->request->post['information_optimblog_breadcrumbs_category_product'];
		} elseif (isset($setting_info['information_optimblog_breadcrumbs_category_product'])) {
			$data['information_optimblog_breadcrumbs_category_product'] = $setting_info['information_optimblog_breadcrumbs_category_product'];
		} else {
			$data['information_optimblog_breadcrumbs_category_product'] = 1;
		}
		
		if (isset($this->request->post['information_optimblog_breadcrumbs_category_information'])) {
			$data['information_optimblog_breadcrumbs_category_information'] = $this->request->post['information_optimblog_breadcrumbs_category_information'];
		} elseif (isset($setting_info['information_optimblog_breadcrumbs_category_information'])) {
			$data['information_optimblog_breadcrumbs_category_information'] = $setting_info['information_optimblog_breadcrumbs_category_information'];
		} else {
			$data['information_optimblog_breadcrumbs_category_information'] = 1;
		}
		
		if (isset($this->request->post['information_optimblog_breadcrumbs_product'])) {
			$data['information_optimblog_breadcrumbs_product'] = $this->request->post['information_optimblog_breadcrumbs_product'];
		} elseif (isset($setting_info['information_optimblog_breadcrumbs_product'])) {
			$data['information_optimblog_breadcrumbs_product'] = $setting_info['information_optimblog_breadcrumbs_product'];
		} else {
			$data['information_optimblog_breadcrumbs_product'] = 1;
		}
		
		if (isset($this->request->post['information_optimblog_breadcrumbs_information'])) {
			$data['information_optimblog_breadcrumbs_information'] = $this->request->post['information_optimblog_breadcrumbs_information'];
		} elseif (isset($setting_info['information_optimblog_breadcrumbs_information'])) {
			$data['information_optimblog_breadcrumbs_information'] = $setting_info['information_optimblog_breadcrumbs_information'];
		} else {
			$data['information_optimblog_breadcrumbs_information'] = 1;
		}
		
		if (isset($this->request->post['information_optimblog_information_author'])) {
			$data['information_optimblog_information_author'] = $this->request->post['information_optimblog_information_author'];
		} elseif (isset($setting_info['information_optimblog_information_author'])) {
			$data['information_optimblog_information_author'] = $setting_info['information_optimblog_information_author'];
		} else {
			$data['information_optimblog_information_author'] = '';
		}

		if (isset($this->request->post['information_optimblog_information_date'])) {
			$data['information_optimblog_information_date'] = $this->request->post['information_optimblog_information_date'];
		} elseif (isset($setting_info['information_optimblog_information_date'])) {
			$data['information_optimblog_information_date'] = $setting_info['information_optimblog_information_date'];
		} else {
			$data['information_optimblog_information_date'] = '';
		}

		if (isset($this->request->post['information_optimblog_information_manufacturer'])) {
			$data['information_optimblog_information_manufacturer'] = $this->request->post['information_optimblog_information_manufacturer'];
		} elseif (isset($setting_info['information_optimblog_information_manufacturer'])) {
			$data['information_optimblog_information_manufacturer'] = $setting_info['information_optimblog_information_manufacturer'];
		} else {
			$data['information_optimblog_information_manufacturer'] = '';
		}

		if (isset($this->request->post['information_optimblog_information_review'])) {
			$data['information_optimblog_information_review'] = $this->request->post['information_optimblog_information_review'];
		} elseif (isset($setting_info['information_optimblog_information_review'])) {
			$data['information_optimblog_information_review'] = $setting_info['information_optimblog_information_review'];
		} else {
			$data['information_optimblog_information_review'] = '';
		}

		if (isset($this->request->post['information_optimblog_review_status'])) {
			$data['information_optimblog_review_status'] = $this->request->post['information_optimblog_review_status'];
		} elseif (isset($setting_info['information_optimblog_review_status'])) {
			$data['information_optimblog_review_status'] = $setting_info['information_optimblog_review_status'];
		} else {
			$data['information_optimblog_review_status'] = '';
		}

		if (isset($this->request->post['information_optimblog_review_guest'])) {
			$data['information_optimblog_review_guest'] = $this->request->post['information_optimblog_review_guest'];
		} elseif (isset($setting_info['information_optimblog_review_guest'])) {
			$data['information_optimblog_review_guest'] = $setting_info['information_optimblog_review_guest'];
		} else {
			$data['information_optimblog_review_guest'] = '';
		}

		if (isset($this->request->post['information_optimblog_captcha'])) {
			$data['information_optimblog_captcha'] = $this->request->post['information_optimblog_captcha'];
		} elseif (isset($setting_info['information_optimblog_captcha'])) {
			$data['information_optimblog_captcha'] = $setting_info['information_optimblog_captcha'];
		} else {
			$data['information_optimblog_captcha'] = '';
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
        
		if (isset($this->request->post['information_optimblog_information_show'])) {
			$data['information_optimblog_information_show'] = $this->request->post['information_optimblog_information_show'];
		} elseif (isset($setting_info['information_optimblog_information_show'])) {
			$data['information_optimblog_information_show'] = $setting_info['information_optimblog_information_show'];
		} else {
			$data['information_optimblog_information_show'] = 1;
		}		

		if (isset($this->request->post['information_optimblog_information_description_length'])) {
			$data['information_optimblog_information_description_length'] = $this->request->post['information_optimblog_information_description_length'];
		} elseif (isset($setting_info['information_optimblog_information_description_length'])) {
			$data['information_optimblog_information_description_length'] = $setting_info['information_optimblog_information_description_length'];
		} else {
			$data['information_optimblog_information_description_length'] = 100;
		}
		
		if (isset($this->request->post['information_optimblog_information_count'])) {
			$data['information_optimblog_information_count'] = $this->request->post['information_optimblog_information_count'];
		} elseif (isset($setting_info['information_optimblog_information_count'])) {
			$data['information_optimblog_information_count'] = $setting_info['information_optimblog_information_count'];
		} else {
			$data['information_optimblog_information_count'] = '';
		}

		if (isset($this->request->post['information_optimblog_share'])) {
			$data['information_optimblog_share'] = $this->request->post['information_optimblog_share'];
		} elseif (isset($setting_info['information_optimblog_share'])) {
			$data['information_optimblog_share'] = $setting_info['information_optimblog_share'];
		} else {
			$data['information_optimblog_share']  = '';
		}
		
		if (isset($this->request->post['information_optimblog_information_thumb'])) {
			$data['information_optimblog_information_thumb'] = $this->request->post['information_optimblog_information_thumb'];
		} elseif (isset($setting_info['information_optimblog_information_thumb'])) {
			$data['information_optimblog_information_thumb'] = $setting_info['information_optimblog_information_thumb'];
		} else {
			$data['information_optimblog_information_thumb']  = '';
		}
		
		if (isset($this->request->post['information_optimblog_information_style'])) {
			$data['information_optimblog_information_style'] = $this->request->post['information_optimblog_information_style'];
		} elseif (isset($setting_info['information_optimblog_information_style'])) {
			$data['information_optimblog_information_style'] = $setting_info['information_optimblog_information_style'];
		} else {
			$data['information_optimblog_information_style'] = array(
				'catalog/view/javascript/jquery/magnific/magnific-popup.css',
				'catalog/view/javascript/jquery/swiper/css/swiper.min.css',
				'catalog/view/javascript/jquery/swiper/css/opencart.css'
			);
		}

		if (isset($this->request->post['information_optimblog_information_script'])) {
			$data['information_optimblog_information_script'] = $this->request->post['information_optimblog_information_script'];
		} elseif (isset($setting_info['information_optimblog_information_script'])) {
			$data['information_optimblog_information_script'] = $setting_info['information_optimblog_information_script'];
		} else {
			$data['information_optimblog_information_script']['header'] = array(
				'catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js',
				'catalog/view/javascript/jquery/swiper/js/swiper.jquery.js'
			);
		}

		if (isset($this->request->post['information_optimblog_category_author'])) {
			$data['information_optimblog_category_author'] = $this->request->post['information_optimblog_category_author'];
		} elseif (isset($setting_info['information_optimblog_category_author'])) {
			$data['information_optimblog_category_author'] = $setting_info['information_optimblog_category_author'];
		} else {
			$data['information_optimblog_category_author'] = '';
		}

		if (isset($this->request->post['information_optimblog_category_date'])) {
			$data['information_optimblog_category_date'] = $this->request->post['information_optimblog_category_date'];
		} elseif (isset($setting_info['information_optimblog_category_date'])) {
			$data['information_optimblog_category_date'] = $setting_info['information_optimblog_category_date'];
		} else {
			$data['information_optimblog_category_date'] = '';
		}

		if (isset($this->request->post['information_optimblog_category_review'])) {
			$data['information_optimblog_category_review'] = $this->request->post['information_optimblog_category_review'];
		} elseif (isset($setting_info['information_optimblog_category_review'])) {
			$data['information_optimblog_category_review'] = $setting_info['information_optimblog_category_review'];
		} else {
			$data['information_optimblog_category_review'] = '';
		}

		if (isset($this->request->post['information_optimblog_category_view'])) {
			$data['information_optimblog_category_view'] = $this->request->post['information_optimblog_category_view'];
		} elseif (isset($setting_info['information_optimblog_category_view'])) {
			$data['information_optimblog_category_view'] = $setting_info['information_optimblog_category_view'];
		} else {
			$data['information_optimblog_category_view'] = 'list';
		}		
		
		if (isset($this->request->post['information_optimblog_category_view_show'])) {
			$data['information_optimblog_category_view_show'] = $this->request->post['information_optimblog_category_view_show'];
		} elseif (isset($setting_info['information_optimblog_category_view_show'])) {
			$data['information_optimblog_category_view_show'] = $setting_info['information_optimblog_category_view_show'];
		} else {
			$data['information_optimblog_category_view_show'] = '';
		}		
		
		if (isset($this->request->post['information_optimblog_information_limit'])) {
			$data['information_optimblog_information_limit'] = $this->request->post['information_optimblog_information_limit'];
		} elseif (isset($setting_info['information_optimblog_information_limit'])) {
			$data['information_optimblog_information_limit'] = $setting_info['information_optimblog_information_limit'];
		} else {
			$data['information_optimblog_information_limit'] = 15;
		}		
		
		if (isset($this->request->post['information_optimblog_category_limit_show'])) {
			$data['information_optimblog_category_limit_show'] = $this->request->post['information_optimblog_category_limit_show'];
		} elseif (isset($setting_info['information_optimblog_category_limit_show'])) {
			$data['information_optimblog_category_limit_show'] = $setting_info['information_optimblog_category_limit_show'];
		} else {
			$data['information_optimblog_category_limit_show'] = '';
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

		if ($data['information_optimblog_review_status']) {
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_desc'),
				'value' => 'rating-DESC'
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_asc'),
				'value' => 'rating-ASC'
			);
		}

		if (isset($this->request->post['information_optimblog_category_sort'])) {
			$data['information_optimblog_category_sort'] = $this->request->post['information_optimblog_category_sort'];
		} elseif (isset($setting_info['information_optimblog_category_sort'])) {
			$data['information_optimblog_category_sort'] = $setting_info['information_optimblog_category_sort'];
		} else {
			$data['information_optimblog_category_sort'] = 'i.sort_order-ASC';		
		}
		
		if (isset($this->request->post['information_optimblog_category_sort_show'])) {
			$data['information_optimblog_category_sort_show'] = $this->request->post['information_optimblog_category_sort_show'];
		} elseif (isset($setting_info['information_optimblog_category_sort_show'])) {
			$data['information_optimblog_category_sort_show'] = $setting_info['information_optimblog_category_sort_show'];
		} else {
			$data['information_optimblog_category_sort_show'] = '';		
		}
		
		// Exclusion Informations
        $this->load->model('catalog/information');

		if (isset($this->request->post['information_optimblog_exclusion_information'])) {
			$exclusion_informations = $this->request->post['information_optimblog_exclusion_information'];
		} elseif (isset($setting_info['information_optimblog_exclusion_information'])) {
			$exclusion_informations = $setting_info['information_optimblog_exclusion_information'];
		} else {
			$exclusion_informations = array();
		}

		$data['information_optimblog_exclusion_informations'] = array();

		foreach ($exclusion_informations as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['information_optimblog_exclusion_informations'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['information_optimblog_exclusion_information_author'])) {
			$exclusion_informations_author = $this->request->post['information_optimblog_exclusion_information_author'];
		} elseif (isset($setting_info['information_optimblog_exclusion_information_author'])) {
			$exclusion_informations_author = $setting_info['information_optimblog_exclusion_information_author'];
		} else {
			$exclusion_informations_author = array();
		}

		$data['information_optimblog_exclusion_informations_author'] = array();

		foreach ($exclusion_informations_author as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['information_optimblog_exclusion_informations_author'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['information_optimblog_exclusion_information_date'])) {
			$exclusion_informations_date = $this->request->post['information_optimblog_exclusion_information_date'];
		} elseif (isset($setting_info['information_optimblog_exclusion_information_date'])) {
			$exclusion_informations_date = $setting_info['information_optimblog_exclusion_information_date'];
		} else {
			$exclusion_informations_date = array();
		}

		$data['information_optimblog_exclusion_informations_date'] = array();

		foreach ($exclusion_informations_date as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['information_optimblog_exclusion_informations_date'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['information_optimblog_exclusion_information_manufacturer'])) {
			$exclusion_informations_manufacturer = $this->request->post['information_optimblog_exclusion_information_manufacturer'];
		} elseif (isset($setting_info['information_optimblog_exclusion_information_manufacturer'])) {
			$exclusion_informations_manufacturer = $setting_info['information_optimblog_exclusion_information_manufacturer'];
		} else {
			$exclusion_informations_manufacturer = array();
		}

		$data['information_optimblog_exclusion_informations_manufacturer'] = array();

		foreach ($exclusion_informations_manufacturer as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['information_optimblog_exclusion_informations_manufacturer'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['information_optimblog_exclusion_information_review'])) {
			$exclusion_informations_review = $this->request->post['information_optimblog_exclusion_information_review'];
		} elseif (isset($setting_info['information_optimblog_exclusion_information_review'])) {
			$exclusion_informations_review = $setting_info['information_optimblog_exclusion_information_review'];
		} else {
			$exclusion_informations_review = array();
		}

		$data['information_optimblog_exclusion_informations_review'] = array();

		foreach ($exclusion_informations_review as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['information_optimblog_exclusion_informations_review'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		// Exclusion Category
        $this->load->model('catalog/category');

		if (isset($this->request->post['information_optimblog_exclusion_category_author'])) {
			$exclusion_categories_author = $this->request->post['information_optimblog_exclusion_category_author'];
		} elseif (isset($setting_info['information_optimblog_exclusion_category_author'])) {
			$exclusion_categories_author = $setting_info['information_optimblog_exclusion_category_author'];
		} else {
			$exclusion_categories_author = array();
		}

		$data['information_optimblog_exclusion_categories_author'] = array();

		foreach ($exclusion_categories_author as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['information_optimblog_exclusion_categories_author'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['information_optimblog_exclusion_category_date'])) {
			$exclusion_categories_date = $this->request->post['information_optimblog_exclusion_category_date'];
		} elseif (isset($setting_info['information_optimblog_exclusion_category_date'])) {
			$exclusion_categories_date = $setting_info['information_optimblog_exclusion_category_date'];
		} else {
			$exclusion_categories_date = array();
		}

		$data['information_optimblog_exclusion_categories_date'] = array();

		foreach ($exclusion_categories_date as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['information_optimblog_exclusion_categories_date'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['information_optimblog_exclusion_category_review'])) {
			$exclusion_categories_review = $this->request->post['information_optimblog_exclusion_category_review'];
		} elseif (isset($setting_info['information_optimblog_exclusion_category_review'])) {
			$exclusion_categories_review = $setting_info['information_optimblog_exclusion_category_review'];
		} else {
			$exclusion_categories_review = array();
		}

		$data['information_optimblog_exclusion_categories_review'] = array();

		foreach ($exclusion_categories_review as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['information_optimblog_exclusion_categories_review'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['information_optimblog_image_category_width'])) {
			$data['information_optimblog_image_category_width'] = $this->request->post['information_optimblog_image_category_width'];
		} elseif (isset($setting_info['information_optimblog_image_category_width'])) {
			$data['information_optimblog_image_category_width'] = $setting_info['information_optimblog_image_category_width'];
		} else {
			$data['information_optimblog_image_category_width'] = 80;		
		}
		
		if (isset($this->request->post['information_optimblog_image_category_height'])) {
			$data['information_optimblog_image_category_height'] = $this->request->post['information_optimblog_image_category_height'];
		} elseif (isset($setting_info['information_optimblog_image_category_height'])) {
			$data['information_optimblog_image_category_height'] = $setting_info['information_optimblog_image_category_height'];
		} else {
			$data['information_optimblog_image_category_height'] = 80;
		}
		
		if (isset($this->request->post['information_optimblog_image_thumb_width'])) {
			$data['information_optimblog_image_thumb_width'] = $this->request->post['information_optimblog_image_thumb_width'];
		} elseif (isset($setting_info['information_optimblog_image_thumb_width'])) {
			$data['information_optimblog_image_thumb_width'] = $setting_info['information_optimblog_image_thumb_width'];
		} else {
			$data['information_optimblog_image_thumb_width'] = 840;
		}
		
		if (isset($this->request->post['information_optimblog_image_thumb_height'])) {
			$data['information_optimblog_image_thumb_height'] = $this->request->post['information_optimblog_image_thumb_height'];
		} elseif (isset($setting_info['information_optimblog_image_thumb_height'])) {
			$data['information_optimblog_image_thumb_height'] = $setting_info['information_optimblog_image_thumb_height'];
		} else {
			$data['information_optimblog_image_thumb_height'] = 240;		
		}
		
		if (isset($this->request->post['information_optimblog_image_popup_width'])) {
			$data['information_optimblog_image_popup_width'] = $this->request->post['information_optimblog_image_popup_width'];
		} elseif (isset($setting_info['information_optimblog_image_popup_width'])) {
			$data['information_optimblog_image_popup_width'] = $setting_info['information_optimblog_image_popup_width'];
		} else {
			$data['information_optimblog_image_popup_width'] = 1680;
		}
		
		if (isset($this->request->post['information_optimblog_image_popup_height'])) {
			$data['information_optimblog_image_popup_height'] = $this->request->post['information_optimblog_image_popup_height'];
		} elseif (isset($setting_info['information_optimblog_image_popup_height'])) {
			$data['information_optimblog_image_popup_height'] = $setting_info['information_optimblog_image_popup_height'];
		} else {
			$data['information_optimblog_image_popup_height'] = 480;
		}
		
		if (isset($this->request->post['information_optimblog_image_information_width'])) {
			$data['information_optimblog_image_information_width'] = $this->request->post['information_optimblog_image_information_width'];
		} elseif (isset($setting_info['information_optimblog_image_information_width'])) {
			$data['information_optimblog_image_information_width'] = $setting_info['information_optimblog_image_information_width'];
		} else {
			$data['information_optimblog_image_information_width'] = 228;
		}
		
		if (isset($this->request->post['information_optimblog_image_information_height'])) {
			$data['information_optimblog_image_information_height'] = $this->request->post['information_optimblog_image_information_height'];
		} elseif (isset($setting_info['information_optimblog_image_information_height'])) {
			$data['information_optimblog_image_information_height'] = $setting_info['information_optimblog_image_information_height'];
		} else {
			$data['information_optimblog_image_information_height'] = 228;
		}
		
		if (isset($this->request->post['information_optimblog_image_additional_width'])) {
			$data['information_optimblog_image_additional_width'] = $this->request->post['information_optimblog_image_additional_width'];
		} elseif (isset($setting_info['information_optimblog_image_additional_width'])) {
			$data['information_optimblog_image_additional_width'] = $setting_info['information_optimblog_image_additional_width'];
		} else {
			$data['information_optimblog_image_additional_width'] = 840;
		}
		
		if (isset($this->request->post['information_optimblog_image_additional_height'])) {
			$data['information_optimblog_image_additional_height'] = $this->request->post['information_optimblog_image_additional_height'];
		} elseif (isset($setting_info['information_optimblog_image_additional_height'])) {
			$data['information_optimblog_image_additional_height'] = $setting_info['information_optimblog_image_additional_height'];
		} else {
			$data['information_optimblog_image_additional_height'] = 240;
		}
		
		if (isset($this->request->post['information_optimblog_image_related_width'])) {
			$data['information_optimblog_image_related_width'] = $this->request->post['information_optimblog_image_related_width'];
		} elseif (isset($setting_info['information_optimblog_image_related_width'])) {
			$data['information_optimblog_image_related_width'] = $setting_info['information_optimblog_image_related_width'];
		} else {
			$data['information_optimblog_image_related_width'] = 200;
		}
		
		if (isset($this->request->post['information_optimblog_image_related_height'])) {
			$data['information_optimblog_image_related_height'] = $this->request->post['information_optimblog_image_related_height'];
		} elseif (isset($setting_info['information_optimblog_image_related_height'])) {
			$data['information_optimblog_image_related_height'] = $setting_info['information_optimblog_image_related_height'];
		} else {
			$data['information_optimblog_image_related_height'] = 200;
		}
		
		if (isset($this->request->post['information_optimblog_image_category_popup_width'])) {
			$data['information_optimblog_image_category_popup_width'] = $this->request->post['information_optimblog_image_category_popup_width'];
		} elseif (isset($setting_info['information_optimblog_image_category_popup_width'])) {
			$data['information_optimblog_image_category_popup_width'] = $setting_info['information_optimblog_image_category_popup_width'];
		} else {
			$data['information_optimblog_image_category_popup_width'] = 500;
		}
		
		if (isset($this->request->post['information_optimblog_image_category_popup_height'])) {
			$data['information_optimblog_image_category_popup_height'] = $this->request->post['information_optimblog_image_category_popup_height'];
		} elseif (isset($setting_info['information_optimblog_image_category_popup_height'])) {
			$data['information_optimblog_image_category_popup_height'] = $setting_info['information_optimblog_image_category_popup_height'];
		} else {
			$data['information_optimblog_image_category_popup_height'] = 500;
		}
		
		if (isset($this->request->post['information_optimblog_image_category_additional_width'])) {
			$data['information_optimblog_image_category_additional_width'] = $this->request->post['information_optimblog_image_category_additional_width'];
		} elseif (isset($setting_info['information_optimblog_image_category_additional_width'])) {
			$data['information_optimblog_image_category_additional_width'] = $setting_info['information_optimblog_image_category_additional_width'];
		} else {
			$data['information_optimblog_image_category_additional_width'] = 74;
		}
		
		if (isset($this->request->post['information_optimblog_image_category_additional_height'])) {
			$data['information_optimblog_image_category_additional_height'] = $this->request->post['information_optimblog_image_category_additional_height'];
		} elseif (isset($setting_info['information_optimblog_image_category_additional_height'])) {
			$data['information_optimblog_image_category_additional_height'] = $setting_info['information_optimblog_image_category_additional_height'];
		} else {
			$data['information_optimblog_image_category_additional_height'] = 74;
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/information/optimblog', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/information/optimblog')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['information_optimblog_information_description_length']) {
			$this->error['description_length'] = $this->language->get('error_limit');
		}

		if (!$this->request->post['information_optimblog_information_limit']) {
			$this->error['information_limit'] = $this->language->get('error_limit');
		}

		if (!$this->request->post['information_optimblog_image_category_width'] || !$this->request->post['information_optimblog_image_category_height']) {
			$this->error['image_category'] = $this->language->get('error_image_category');
		}

		if (!$this->request->post['information_optimblog_image_thumb_width'] || !$this->request->post['information_optimblog_image_thumb_height']) {
			$this->error['image_thumb'] = $this->language->get('error_image_thumb');
		}

		if (!$this->request->post['information_optimblog_image_popup_width'] || !$this->request->post['information_optimblog_image_popup_height']) {
			$this->error['image_popup'] = $this->language->get('error_image_popup');
		}

		if (!$this->request->post['information_optimblog_image_information_width'] || !$this->request->post['information_optimblog_image_information_height']) {
			$this->error['image_information'] = $this->language->get('error_image_information');
		}

		if (!$this->request->post['information_optimblog_image_additional_width'] || !$this->request->post['information_optimblog_image_additional_height']) {
			$this->error['image_additional'] = $this->language->get('error_image_additional');
		}

		if (!$this->request->post['information_optimblog_image_related_width'] || !$this->request->post['information_optimblog_image_related_height']) {
			$this->error['image_related'] = $this->language->get('error_image_related');
		}

		if (!$this->request->post['information_optimblog_image_category_popup_width'] || !$this->request->post['information_optimblog_image_category_popup_height']) {
			$this->error['image_category_popup'] = $this->language->get('error_image_category_popup');
		}

		if (!$this->request->post['information_optimblog_image_category_additional_width'] || !$this->request->post['information_optimblog_image_category_additional_height']) {
			$this->error['image_category_additional'] = $this->language->get('error_image_category_additional');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('extension/information/optimblog');
        
		$this->model_extension_information_optimblog->createTables();

		$this->model_extension_information_optimblog->update();

		$this->load->model('setting/event');

		$this->model_setting_event->addEvent('optimblog_catalog_view_header', 'catalog/view/common/header/before', 'extension/information/optimblog/viewHeaderBefore');
		$this->model_setting_event->addEvent('optimblog_catalog_view_category', 'catalog/view/product/category/before', 'extension/information/optimblog/viewCategory');
//		$this->model_setting_event->addEvent('optimblog_catalog_view_category_after', 'catalog/view/product/category/after', 'extension/information/optimblog/viewCategoryAfter');
		$this->model_setting_event->addEvent('optimblog_catalog_view_product', 'catalog/view/product/product/before', 'extension/information/optimblog/viewProduct');
		$this->model_setting_event->addEvent('optimblog_catalog_view_information', 'catalog/view/information/information/before', 'extension/information/optimblog/viewInformationBefore');
//		$this->model_setting_event->addEvent('optimblog_catalog_view_information_after', 'catalog/view/information/information/after', 'extension/information/optimblog/viewInformationAfter');

		$this->model_setting_event->addEvent('optimblog_catalog_information_review', 'catalog/controller/information/information/review/before', 'extension/information/optimblog/informationReview');
		$this->model_setting_event->addEvent('optimblog_catalog_information_write', 'catalog/controller/information/information/write/before', 'extension/information/optimblog/informationWrite');
//		$this->model_setting_event->addEvent('optimblog_catalog_information', 'catalog/controller/information/information/before', 'extension/information/optimblog/informationBefore');

		$this->model_setting_event->addEvent('optimblog_catalog_model_information_get', 'catalog/model/catalog/information/getInformation/before', 'extension/information/optimblog/getInformation');
		$this->model_setting_event->addEvent('optimblog_catalog_model_informations_get', 'catalog/model/catalog/information/getInformations/before', 'extension/information/optimblog/getInformations');
		$this->model_setting_event->addEvent('optimblog_catalog_model_product_get', 'catalog/model/catalog/product/getProduct/before', 'extension/information/optimblog/getProduct');
		$this->model_setting_event->addEvent('optimblog_catalog_model_product_related', 'catalog/model/catalog/product/getProductRelated/before', 'extension/information/optimblog/getProductRelated');
		$this->model_setting_event->addEvent('optimblog_catalog_model_product_review', 'catalog/model/catalog/review/getReviewsByProductId/before', 'extension/information/optimblog/getReviewsByProductId');
		$this->model_setting_event->addEvent('optimblog_catalog_model_product_review_total', 'catalog/model/catalog/review/getTotalReviewsByProductId/before', 'extension/information/optimblog/getTotalReviewsByProductId');
	}

	public function uninstall() {
		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('optimblog_catalog_view_header');
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
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_product_get');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_product_related');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_product_review');
		$this->model_setting_event->deleteEventByCode('optimblog_catalog_model_product_review_total');
	}

	public function export() {
		$this->load->language('extension/information/optimblog');

		if (!$this->user->hasPermission('modify', 'extension/information/optimblog')) {
			$this->session->data['error'] = $this->language->get('error_permission');

			$this->response->redirect($this->url->link('extension/information/optimblog', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true));
		} else {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename="optimblog_setting_backup_' . $this->request->get['store_id'] . '_' . date('Y-m-d_H-i-s', time()) . '.txt"');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->load->model('setting/setting');

			$this->response->setOutput(json_encode($this->model_setting_setting->getSetting('information_optimblog', $this->request->get['store_id'])));
		}
	}	

	public function import() {
		$this->load->language('extension/information/optimblog');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'extension/information/optimblog')) {
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

				$this->model_setting_setting->editSetting('information_optimblog', $setting, $this->request->get['store_id']);

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
}