<?php
/**
 * @package    OptimBlog
 * @version    3.0.0.2
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
class ControllerExtensionModuleFeaturedInformation extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured_information');

		$this->load->model('catalog/information');

		$this->load->model('tool/image');

		$data['informations'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['information'])) {
			$informations = array_slice($setting['information'], 0, (int)$setting['limit']);

			foreach ($informations as $information_id) {
				$information_info = $this->model_catalog_information->getInformation($information_id);

				if ($information_info) {
					if ($information_info['image']) {
						$image = $this->model_tool_image->resize($information_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = false;
					}

					if ($this->config->get('information_review_status')) {
						$rating = $information_info['rating'];
					} else {
						$rating = false;
					}

					$data['informations'][] = array(
						'information_id' => $information_info['information_id'],
						'thumb'          => $image,
						'title'          => $information_info['title'],
						'description'    => !empty($information_info['short_description']) ? trim(html_entity_decode($information_info['short_description'], ENT_QUOTES, 'UTF-8')) : utf8_substr(trim(strip_tags(html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('information_description_length')) . '..',
						'user_id'        => $information_info['user_id'],
						'author'         => $information_info['author'],
						'date_added'     => date($this->language->get('date_format_short'), strtotime($information_info['date_added'])),
						'reviews'        => sprintf($this->language->get('text_review'), $information_info['reviews']),
						'rating'         => $information_info['rating'],
						'href'           => $this->url->link('information/information', '&information_id=' . $information_info['information_id'])
					);
				}
			}
		}

		if ($data['informations']) {
			if ($setting['title'][$this->config->get('config_language_id')]) {
				$data['heading_title'] = html_entity_decode($setting['title'][$this->config->get('config_language_id')]);
			}

			$data['author'] = $this->config->get('information_category_author');
			$data['date'] = $this->config->get('information_category_date');
			$data['review'] = $this->config->get('information_category_review');

			return $this->load->view('extension/module/featured_information', $data);
		}
	}
}