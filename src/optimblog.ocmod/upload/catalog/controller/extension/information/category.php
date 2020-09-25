<?php
/**
 * @package    OptimBlog
 * @version    3.0.1.4
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
class ControllerExtensionInformationCategory extends Controller {
	public function index() {
		$this->load->language('product/category');
		$this->load->language('information/category');

		$this->load->model('catalog/category');

		$this->load->model('tool/image');

		$this->load->model('extension/information/optimblog_category');
		$this->load->model('extension/information/optimblog_information');

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if ($this->config->get('information_optimblog_category_sort')) {
			$category_sort = explode('-', (string)$this->config->get('information_optimblog_category_sort'));
			$sort = $category_sort[0];
			$order = $category_sort[1];
		} else {
			$sort = 'i.sort_order';
			$order = 'ASC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('information_optimblog_information_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			if ($this->config->get('information_optimblog_breadcrumbs_category_information')) {
				$category_path = $this->model_extension_information_optimblog_category->getCategoryPath($category_id);
            
				$this->request->get['path'] = $category_path;

				$parts = explode('_', (string)$this->request->get['path']);

				(int)array_pop($parts);
			} elseif (!$this->config->get('information_optimblog_breadcrumbs_category_information') && $this->config->get('information_optimblog_canonical_category_information')) {
				$category_path = $this->model_extension_information_optimblog_category->getCategoryPath($category_id);
			}

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					);
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info['information']) {
			$this->document->setTitle($category_info['meta_title']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);
			$this->document->addScript('catalog/view/javascript/optimblog.js');
			
			$data['heading_title'] = $category_info['header'] ? $category_info['header'] : $category_info['name'];

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['images'] = array();

			$results = $this->model_extension_information_optimblog_category->getCategoryImages($category_id);

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('information_optimblog_image_category_popup_width'), $this->config->get('information_optimblog_image_category_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('information_optimblog_image_category_additional_width'), $this->config->get('information_optimblog_image_category_additional_height'))
				);
			}
        
			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);

				$data['categories'][] = array(
					'name' => $result['name'] . ($this->config->get('information_optimblog_information_count') ? ' (' . $this->model_extension_information_optimblog_information->getTotalInformations($filter_data) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
				);
			}

			// Information
			$data['informations'] = array();

			if ($this->config->get('information_optimblog_information_show')) {
				$filter_data = array(
					'filter_category_id' => $category_id,
					'filter_filter'      => $filter,
					'sort'               => $sort,
					'order'              => $order,
					'start'              => ($page - 1) * $limit,
					'limit'              => $limit
				);

				$information_total = $this->model_extension_information_optimblog_information->getTotalInformations($filter_data);

				$results = $this->model_extension_information_optimblog_information->getInformations($filter_data);

				foreach ($results as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('information_optimblog_image_information_width'), $this->config->get('information_optimblog_image_information_height'));
					} else {
						$image = false;
					}

					if ($this->config->get('information_optimblog_review_status')) {
						$rating = (int)$result['rating'];
					} else {
						$rating = false;
					}

					$data['informations'][] = array(
						'information_id' => $result['information_id'],
						'thumb'          => $image,
						'title'          => $result['title'],
						'description'    => !empty($result['short_description']) ? trim(html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8')) : utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('information_optimblog_information_description_length')) . '..',
						'user_id'        => $result['user_id'],
						'author'         => $result['author'],
						'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
						'reviews'        => sprintf($this->language->get('text_review'), $result['reviews']),
						'rating'         => $result['rating'],
						'href'           => $this->url->link('information/information', 'path=' . $this->request->get['path'] . '&information_id=' . $result['information_id'] . $url)
					);
				}

				$url = '';

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['sorts'] = array();

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_default'),
					'value' => 'i.sort_order-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=i.sort_order&order=ASC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_name_asc'),
					'value' => 'id.title-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=id.title&order=ASC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_name_desc'),
					'value' => 'id.title-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=id.title&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_date_asc'),
					'value' => 'i.date_added-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=i.date_added&order=ASC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_date_desc'),
					'value' => 'i.date_added-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=i.date_added&order=DESC' . $url)
				);

				if ($this->config->get('information_optimblog_review_status')) {
					$data['sorts'][] = array(
						'text'  => $this->language->get('text_rating_desc'),
						'value' => 'rating-DESC',
						'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
					);

					$data['sorts'][] = array(
						'text'  => $this->language->get('text_rating_asc'),
						'value' => 'rating-ASC',
						'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
					);
				}

				$url = '';

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				$data['limits'] = array();

				$limits = array_unique(array($this->config->get('information_optimblog_information_limit'), 25, 50, 75, 100));

				sort($limits);

				foreach($limits as $value) {
					$data['limits'][] = array(
						'text'  => $value,
						'value' => $value,
						'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
					);
				}

				$url = '';

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$pagination = new Pagination();
				$pagination->total = $information_total;
				$pagination->page = $page;
				$pagination->limit = $limit;
				$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

				$data['pagination'] = $pagination->render();

				$data['results'] = sprintf($this->language->get('text_pagination'), ($information_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($information_total - $limit)) ? $information_total : ((($page - 1) * $limit) + $limit), $information_total, ceil($information_total / $limit));

				// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
				if ($this->config->get('information_optimblog_canonical_category_information')) {
					if ($page == 1) {
			   	 		$this->document->addLink($this->url->link('product/category', 'path=' . $category_path), 'canonical');
					} else {
						$this->document->addLink($this->url->link('product/category', 'path=' . $category_path . '&page='. $page), 'canonical');
					}
			
					if ($page > 1) {
			    		$this->document->addLink($this->url->link('product/category', 'path=' . $category_path . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
					}

					if ($limit && ceil($information_total / $limit) > $page) {
			    		$this->document->addLink($this->url->link('product/category', 'path=' . $category_path . '&page='. ($page + 1)), 'next');
					}
				} else {
					if ($page == 1) {
			   	 		$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');
					} else {
						$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
					}
			
					if ($page > 1) {
			    		$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
					}

					if ($limit && ceil($information_total / $limit) > $page) {
			    		$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
					}
				}

				$data['sort'] = $sort;
				$data['order'] = $order;
				$data['limit'] = $limit;

				$data['sort_show'] = $this->config->get('information_optimblog_category_sort_show');
				$data['limit_show'] = $this->config->get('information_optimblog_category_limit_show');
				$data['view_show'] = $this->config->get('information_optimblog_category_view_show');
				$data['view'] = $this->config->get('information_optimblog_category_view');

				if ($this->config->get('information_optimblog_category_author') && !empty($this->config->get('information_optimblog_exclusion_category_author')) && in_array($category_id, $this->config->get('information_optimblog_exclusion_category_author'))) {
					$data['show_author'] = false;
				} elseif (!$this->config->get('information_optimblog_category_author') && !empty($this->config->get('information_optimblog_exclusion_category_author')) && in_array($category_id, $this->config->get('information_optimblog_exclusion_category_author'))){
					$data['show_author'] = true;
				} else {
					$data['show_author'] = $this->config->get('information_optimblog_category_author');
				}

				if ($this->config->get('information_optimblog_category_date') && !empty($this->config->get('information_optimblog_exclusion_category_date')) && in_array($category_id, $this->config->get('information_optimblog_exclusion_category_date'))) {
					$data['show_date'] = false;
				} elseif (!$this->config->get('information_optimblog_category_date') && !empty($this->config->get('information_optimblog_exclusion_category_date')) && in_array($category_id, $this->config->get('information_optimblog_exclusion_category_date'))){
					$data['show_date'] = true;
				} else {
					$data['show_date'] = $this->config->get('information_optimblog_category_date');
				}

				if ($this->config->get('information_optimblog_category_review') && !empty($this->config->get('information_optimblog_exclusion_category_review')) && in_array($category_id, $this->config->get('information_optimblog_exclusion_category_review'))) {
					$data['show_review'] = false;
				} elseif (!$this->config->get('information_optimblog_category_review') && !empty($this->config->get('information_optimblog_exclusion_category_review')) && in_array($category_id, $this->config->get('information_optimblog_exclusion_category_review'))){
					$data['show_review'] = true;
				} else {
					$data['show_review'] = $this->config->get('information_optimblog_category_review');
				}
			}

			$data['category_information'] = true;

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

//			$this->response->setOutput($this->load->view('product/category', $data));
			$this->response->setOutput($this->load->view('information/category', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/category', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}
