<?php
/**
 * @package    OptimBlog
 * @version    3.0.0.8
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
class ControllerExtensionInformationSetting extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/information/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('information', $this->request->post, $this->request->get['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=information', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['limit_admin'])) {
			$data['error_limit_admin'] = $this->error['information_limit'];
		} else {
			$data['error_limit_admin'] = '';
		}

		if (isset($this->error['information_limit'])) {
			$data['error_information_limit'] = $this->error['information_limit'];
		} else {
			$data['error_information_limit'] = '';
		}

		if (isset($this->error['information_description_length'])) {
			$data['error_information_description_length'] = $this->error['information_description_length'];
		} else {
			$data['error_information_description_length'] = '';
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
			'href' => $this->url->link('extension/information/setting', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true)
		);

		$data['action'] = $this->url->link('extension/information/setting', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=information', true);

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$setting_info = $this->model_setting_setting->getSetting('information', $this->request->get['store_id']);
		}
		
		if (isset($this->request->post['information_setting_status'])) {
			$data['information_setting_status'] = $this->request->post['information_setting_status'];
		} elseif (isset($setting_info['information_setting_status'])) {
			$data['information_setting_status'] = $setting_info['information_setting_status'];
		} else {
			$data['information_setting_status'] = '';
		}
		
		if (isset($this->request->post['information_information_author'])) {
			$data['information_information_author'] = $this->request->post['information_information_author'];
		} elseif (isset($setting_info['information_information_author'])) {
			$data['information_information_author'] = $setting_info['information_information_author'];
		} else {
			$data['information_information_author'] = '';
		}

		if (isset($this->request->post['information_information_date'])) {
			$data['information_information_date'] = $this->request->post['information_information_date'];
		} elseif (isset($setting_info['information_information_date'])) {
			$data['information_information_date'] = $setting_info['information_information_date'];
		} else {
			$data['information_information_date'] = '';
		}

		if (isset($this->request->post['information_information_review'])) {
			$data['information_information_review'] = $this->request->post['information_information_review'];
		} elseif (isset($setting_info['information_information_review'])) {
			$data['information_information_review'] = $setting_info['information_information_review'];
		} else {
			$data['information_information_review'] = '';
		}

		if (isset($this->request->post['information_information_manufacturer'])) {
			$data['information_information_manufacturer'] = $this->request->post['information_information_manufacturer'];
		} elseif (isset($setting_info['information_information_manufacturer'])) {
			$data['information_information_manufacturer'] = $setting_info['information_information_manufacturer'];
		} else {
			$data['information_information_manufacturer'] = '';
		}

		if (isset($this->request->post['information_category_show'])) {
			$data['information_category_show'] = $this->request->post['information_category_show'];
		} elseif (isset($setting_info['information_category_show'])) {
			$data['information_category_show'] = $setting_info['information_category_show'];
		} else {
			$data['information_category_show'] = 1;
		}		

		if (isset($this->request->post['information_limit'])) {
			$data['information_limit'] = $this->request->post['information_limit'];
		} elseif (isset($setting_info['information_limit'])) {
			$data['information_limit'] = $setting_info['information_limit'];
		} else {
			$data['information_limit'] = 15;
		}		
		
		if (isset($this->request->post['information_category_limit_show'])) {
			$data['information_category_limit_show'] = $this->request->post['information_category_limit_show'];
		} elseif (isset($setting_info['information_category_limit_show'])) {
			$data['information_category_limit_show'] = $setting_info['information_category_limit_show'];
		} else {
			$data['information_category_limit_show'] = '';
		}		
		
		if (isset($this->request->post['information_category_view'])) {
			$data['information_category_view'] = $this->request->post['information_category_view'];
		} elseif (isset($setting_info['information_category_view'])) {
			$data['information_category_view'] = $setting_info['information_category_view'];
		} else {
			$data['information_category_view'] = 'list';
		}		
		
		if (isset($this->request->post['information_category_view_show'])) {
			$data['information_category_view_show'] = $this->request->post['information_category_view_show'];
		} elseif (isset($setting_info['information_category_view_show'])) {
			$data['information_category_view_show'] = $setting_info['information_category_view_show'];
		} else {
			$data['information_category_view_show'] = '';
		}		
		
		if (isset($this->request->post['information_description_length'])) {
			$data['information_description_length'] = $this->request->post['information_description_length'];
		} elseif (isset($setting_info['information_description_length'])) {
			$data['information_description_length'] = $setting_info['information_description_length'];
		} else {
			$data['information_description_length'] = 100;
		}
		
		if (isset($this->request->post['information_count'])) {
			$data['information_count'] = $this->request->post['information_count'];
		} elseif (isset($setting_info['information_count'])) {
			$data['information_count'] = $setting_info['information_count'];
		} else {
			$data['information_count'] = '';
		}

		if (isset($this->request->post['information_category_author'])) {
			$data['information_category_author'] = $this->request->post['information_category_author'];
		} elseif (isset($setting_info['information_category_author'])) {
			$data['information_category_author'] = $setting_info['information_category_author'];
		} else {
			$data['information_category_author'] = '';
		}

		if (isset($this->request->post['information_category_date'])) {
			$data['information_category_date'] = $this->request->post['information_category_date'];
		} elseif (isset($setting_info['information_category_date'])) {
			$data['information_category_date'] = $setting_info['information_category_date'];
		} else {
			$data['information_category_date'] = '';
		}

		if (isset($this->request->post['information_category_review'])) {
			$data['information_category_review'] = $this->request->post['information_category_review'];
		} elseif (isset($setting_info['information_category_review'])) {
			$data['information_category_review'] = $setting_info['information_category_review'];
		} else {
			$data['information_category_review'] = '';
		}

		if (isset($this->request->post['information_review_status'])) {
			$data['information_review_status'] = $this->request->post['information_review_status'];
		} elseif (isset($setting_info['information_review_status'])) {
			$data['information_review_status'] = $setting_info['information_review_status'];
		} else {
			$data['information_review_status'] = '';
		}

		if (isset($this->request->post['information_review_guest'])) {
			$data['information_review_guest'] = $this->request->post['information_review_guest'];
		} elseif (isset($setting_info['information_review_guest'])) {
			$data['information_review_guest'] = $setting_info['information_review_guest'];
		} else {
			$data['information_review_guest'] = '';
		}

		if (isset($this->request->post['information_captcha'])) {
			$data['information_captcha'] = $this->request->post['information_captcha'];
		} elseif (isset($setting_info['information_captcha'])) {
			$data['information_captcha'] = $setting_info['information_captcha'];
		} else {
			$data['information_captcha'] = '';
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
        
		// Exclusion Informations
        $this->load->model('catalog/information');

		if (isset($this->request->post['information_exclusion'])) {
			$exclusion_informations = $this->request->post['information_exclusion'];
		} elseif (isset($setting_info['information_without_review'])) {
			$exclusion_informations = $setting_info['information_without_review'];
		} elseif (isset($setting_info['information_exclusion'])) {
			$exclusion_informations = $setting_info['information_exclusion'];
		} else {
			$exclusion_informations = array();
		}

		$data['exclusion_informations'] = array();

		foreach ($exclusion_informations as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['exclusion_informations'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['information_exclusion_information_author'])) {
			$exclusion_informations_author = $this->request->post['information_exclusion_information_author'];
		} elseif (isset($setting_info['information_exclusion_information_author'])) {
			$exclusion_informations_author = $setting_info['information_exclusion_information_author'];
		} else {
			$exclusion_informations_author = array();
		}

		$data['exclusion_informations_author'] = array();

		foreach ($exclusion_informations_author as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['exclusion_informations_author'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['information_exclusion_information_author'])) {
			$exclusion_informations_author = $this->request->post['information_exclusion_information_author'];
		} elseif (isset($setting_info['information_exclusion_information_author'])) {
			$exclusion_informations_author = $setting_info['information_exclusion_information_author'];
		} else {
			$exclusion_informations_author = array();
		}

		$data['exclusion_informations_author'] = array();

		foreach ($exclusion_informations_author as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['exclusion_informations_author'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['information_exclusion_information_date'])) {
			$exclusion_informations_date = $this->request->post['information_exclusion_information_date'];
		} elseif (isset($setting_info['information_exclusion_information_date'])) {
			$exclusion_informations_date = $setting_info['information_exclusion_information_date'];
		} else {
			$exclusion_informations_date = array();
		}

		$data['exclusion_informations_date'] = array();

		foreach ($exclusion_informations_date as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['exclusion_informations_date'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['information_exclusion_information_review'])) {
			$exclusion_informations_review = $this->request->post['information_exclusion_information_review'];
		} elseif (isset($setting_info['information_exclusion_information_review'])) {
			$exclusion_informations_review = $setting_info['information_exclusion_information_review'];
		} else {
			$exclusion_informations_review = array();
		}

		$data['exclusion_informations_review'] = array();

		foreach ($exclusion_informations_review as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['exclusion_informations_review'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		if (isset($this->request->post['information_exclusion_information_manufacturer'])) {
			$exclusion_informations_manufacturer = $this->request->post['information_exclusion_information_manufacturer'];
		} elseif (isset($setting_info['information_exclusion_information_manufacturer'])) {
			$exclusion_informations_manufacturer = $setting_info['information_exclusion_information_manufacturer'];
		} else {
			$exclusion_informations_manufacturer = array();
		}

		$data['exclusion_informations_manufacturer'] = array();

		foreach ($exclusion_informations_manufacturer as $information_id) {
			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$data['exclusion_informations_manufacturer'][] = array(
					'information_id' => $information_info['information_id'],
					'title'          => $information_info['title']
				);
			}
		}

		// Exclusion Category
        $this->load->model('catalog/category');

		if (isset($this->request->post['information_exclusion_category_author'])) {
			$exclusion_categories_author = $this->request->post['information_exclusion_category_author'];
		} elseif (isset($setting_info['information_exclusion_category_author'])) {
			$exclusion_categories_author = $setting_info['information_exclusion_category_author'];
		} else {
			$exclusion_categories_author = array();
		}

		$data['exclusion_categories_author'] = array();

		foreach ($exclusion_categories_author as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['exclusion_categories_author'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['information_exclusion_category_date'])) {
			$exclusion_categories_date = $this->request->post['information_exclusion_category_date'];
		} elseif (isset($setting_info['information_exclusion_category_date'])) {
			$exclusion_categories_date = $setting_info['information_exclusion_category_date'];
		} else {
			$exclusion_categories_date = array();
		}

		$data['exclusion_categories_date'] = array();

		foreach ($exclusion_categories_date as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['exclusion_categories_date'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
		}

		if (isset($this->request->post['information_exclusion_category_review'])) {
			$exclusion_categories_review = $this->request->post['information_exclusion_category_review'];
		} elseif (isset($setting_info['information_exclusion_category_review'])) {
			$exclusion_categories_review = $setting_info['information_exclusion_category_review'];
		} else {
			$exclusion_categories_review = array();
		}

		$data['exclusion_categories_review'] = array();

		foreach ($exclusion_categories_review as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['exclusion_categories_review'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => $category_info['name']
				);
			}
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

		if ($data['information_review_status']) {
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_desc'),
				'value' => 'rating-DESC'
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_asc'),
				'value' => 'rating-ASC'
			);
		}

		if (isset($this->request->post['information_category_sort'])) {
			$data['information_category_sort'] = $this->request->post['information_category_sort'];
		} elseif (isset($setting_info['information_category_sort'])) {
			$data['information_category_sort'] = $setting_info['information_category_sort'];
		} else {
			$data['information_category_sort'] = 'i.sort_order-ASC';		
		}
		
		if (isset($this->request->post['information_category_sort_show'])) {
			$data['information_category_sort_show'] = $this->request->post['information_category_sort_show'];
		} elseif (isset($setting_info['information_category_sort_show'])) {
			$data['information_category_sort_show'] = $setting_info['information_category_sort_show'];
		} else {
			$data['information_category_sort_show'] = '';		
		}
		
		if (isset($this->request->post['information_image_category_width'])) {
			$data['information_image_category_width'] = $this->request->post['information_image_category_width'];
		} elseif (isset($setting_info['information_image_category_width'])) {
			$data['information_image_category_width'] = $setting_info['information_image_category_width'];
		} else {
			$data['information_image_category_width'] = 80;		
		}
		
		if (isset($this->request->post['information_image_category_height'])) {
			$data['information_image_category_height'] = $this->request->post['information_image_category_height'];
		} elseif (isset($setting_info['information_image_category_height'])) {
			$data['information_image_category_height'] = $setting_info['information_image_category_height'];
		} else {
			$data['information_image_category_height'] = 80;
		}
		
		if (isset($this->request->post['information_image_thumb_width'])) {
			$data['information_image_thumb_width'] = $this->request->post['information_image_thumb_width'];
		} elseif (isset($setting_info['information_image_thumb_width'])) {
			$data['information_image_thumb_width'] = $setting_info['information_image_thumb_width'];
		} else {
			$data['information_image_thumb_width'] = 228;
		}
		
		if (isset($this->request->post['information_image_thumb_height'])) {
			$data['information_image_thumb_height'] = $this->request->post['information_image_thumb_height'];
		} elseif (isset($setting_info['information_image_thumb_height'])) {
			$data['information_image_thumb_height'] = $setting_info['information_image_thumb_height'];
		} else {
			$data['information_image_thumb_height'] = 228;		
		}
		
		if (isset($this->request->post['information_image_popup_width'])) {
			$data['information_image_popup_width'] = $this->request->post['information_image_popup_width'];
		} elseif (isset($setting_info['information_image_popup_width'])) {
			$data['information_image_popup_width'] = $setting_info['information_image_popup_width'];
		} else {
			$data['information_image_popup_width'] = 500;
		}
		
		if (isset($this->request->post['information_image_popup_height'])) {
			$data['information_image_popup_height'] = $this->request->post['information_image_popup_height'];
		} elseif (isset($setting_info['information_image_popup_height'])) {
			$data['information_image_popup_height'] = $setting_info['information_image_popup_height'];
		} else {
			$data['information_image_popup_height'] = 500;
		}
		
		if (isset($this->request->post['information_image_information_width'])) {
			$data['information_image_information_width'] = $this->request->post['information_image_information_width'];
		} elseif (isset($setting_info['information_image_information_width'])) {
			$data['information_image_information_width'] = $setting_info['information_image_information_width'];
		} else {
			$data['information_image_information_width'] = 228;
		}
		
		if (isset($this->request->post['information_image_information_height'])) {
			$data['information_image_information_height'] = $this->request->post['information_image_information_height'];
		} elseif (isset($setting_info['information_image_information_height'])) {
			$data['information_image_information_height'] = $setting_info['information_image_information_height'];
		} else {
			$data['information_image_information_height'] = 228;
		}
		
		if (isset($this->request->post['information_image_additional_width'])) {
			$data['information_image_additional_width'] = $this->request->post['information_image_additional_width'];
		} elseif (isset($setting_info['information_image_additional_width'])) {
			$data['information_image_additional_width'] = $setting_info['information_image_additional_width'];
		} else {
			$data['information_image_additional_width'] = 74;
		}
		
		if (isset($this->request->post['information_image_additional_height'])) {
			$data['information_image_additional_height'] = $this->request->post['information_image_additional_height'];
		} elseif (isset($setting_info['information_image_additional_height'])) {
			$data['information_image_additional_height'] = $setting_info['information_image_additional_height'];
		} else {
			$data['information_image_additional_height'] = 74;
		}
		
		if (isset($this->request->post['information_image_related_width'])) {
			$data['information_image_related_width'] = $this->request->post['information_image_related_width'];
		} elseif (isset($setting_info['information_image_related_width'])) {
			$data['information_image_related_width'] = $setting_info['information_image_related_width'];
		} else {
			$data['information_image_related_width'] = 80;
		}
		
		if (isset($this->request->post['information_image_related_height'])) {
			$data['information_image_related_height'] = $this->request->post['information_image_related_height'];
		} elseif (isset($setting_info['information_image_related_height'])) {
			$data['information_image_related_height'] = $setting_info['information_image_related_height'];
		} else {
			$data['information_image_related_height'] = 80;
		}
		
		if (isset($this->request->post['information_image_category_popup_width'])) {
			$data['information_image_category_popup_width'] = $this->request->post['information_image_category_popup_width'];
		} elseif (isset($setting_info['information_image_category_popup_width'])) {
			$data['information_image_category_popup_width'] = $setting_info['information_image_category_popup_width'];
		} else {
			$data['information_image_category_popup_width'] = 500;
		}
		
		if (isset($this->request->post['information_image_category_popup_height'])) {
			$data['information_image_category_popup_height'] = $this->request->post['information_image_category_popup_height'];
		} elseif (isset($setting_info['information_image_category_popup_height'])) {
			$data['information_image_category_popup_height'] = $setting_info['information_image_category_popup_height'];
		} else {
			$data['information_image_category_popup_height'] = 500;
		}
		
		if (isset($this->request->post['information_image_category_additional_width'])) {
			$data['information_image_category_additional_width'] = $this->request->post['information_image_category_additional_width'];
		} elseif (isset($setting_info['information_image_category_additional_width'])) {
			$data['information_image_category_additional_width'] = $setting_info['information_image_category_additional_width'];
		} else {
			$data['information_image_category_additional_width'] = 74;
		}
		
		if (isset($this->request->post['information_image_category_additional_height'])) {
			$data['information_image_category_additional_height'] = $this->request->post['information_image_category_additional_height'];
		} elseif (isset($setting_info['information_image_category_additional_height'])) {
			$data['information_image_category_additional_height'] = $setting_info['information_image_category_additional_height'];
		} else {
			$data['information_image_category_additional_height'] = 74;
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/information/setting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/information/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['information_limit']) {
			$this->error['information_limit'] = $this->language->get('error_limit');
		}

		if (!$this->request->post['information_description_length']) {
			$this->error['information_description_length'] = $this->language->get('error_limit');
		}

		if (!$this->request->post['information_image_category_width'] || !$this->request->post['information_image_category_height']) {
			$this->error['image_category'] = $this->language->get('error_image_category');
		}

		if (!$this->request->post['information_image_thumb_width'] || !$this->request->post['information_image_thumb_height']) {
			$this->error['image_thumb'] = $this->language->get('error_image_thumb');
		}

		if (!$this->request->post['information_image_popup_width'] || !$this->request->post['information_image_popup_height']) {
			$this->error['image_popup'] = $this->language->get('error_image_popup');
		}

		if (!$this->request->post['information_image_information_width'] || !$this->request->post['information_image_information_height']) {
			$this->error['image_information'] = $this->language->get('error_image_information');
		}

		if (!$this->request->post['information_image_additional_width'] || !$this->request->post['information_image_additional_height']) {
			$this->error['image_additional'] = $this->language->get('error_image_additional');
		}

		if (!$this->request->post['information_image_related_width'] || !$this->request->post['information_image_related_height']) {
			$this->error['image_related'] = $this->language->get('error_image_related');
		}

		if (!$this->request->post['information_image_category_popup_width'] || !$this->request->post['information_image_category_popup_height']) {
			$this->error['image_category_popup'] = $this->language->get('error_image_category_popup');
		}

		if (!$this->request->post['information_image_category_additional_width'] || !$this->request->post['information_image_category_additional_height']) {
			$this->error['image_category_additional'] = $this->language->get('error_image_category_additional');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('extension/information/setting');
        
		$this->model_extension_information_setting->createTables();
	}
/**
	public function uninstall() {
		$this->load->model('extension/information/setting');
        
		$results = $this->model_extension_information_setting->getInformationLayouts();
        
        if ($results) {
            $this->load->model('design/layout');
            
            foreach ($results as $layout_id) {
                $this->model_design_layout->deleteLayout($layout_id);
            }
        }
	}
*/
}