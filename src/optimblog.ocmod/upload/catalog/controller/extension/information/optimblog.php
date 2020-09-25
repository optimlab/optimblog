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
	// language/product/product/after
	public function languageProduct(&$route) {
		$this->load->language('extension/information/optimblog_product');
	}

	// language/information/information/after
	public function languageInformation(&$route) {
		$this->load->language('extension/information/optimblog_information');
	}

	// language/mail/review/after
	public function languageReview(&$route) {
		$this->load->language('extension/information/optimblog_review');
	}

	// controller/product/category/before
	public function controllerCategoryType(&$route, &$data) {
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$this->load->model('catalog/category');

			$category_info = $this->model_catalog_category->getCategory($category_id);

			if (!empty($category_info['information'])) {
				$route = 'extension/information/category';
				$this->request->get['route'] = 'extension/information/category';
			}
		}
	}

	// view/common/header/before
	public function viewHeaderBefore(&$route, &$data) {
		// Canonical Category Product
		if ((isset($this->request->get['route']) && $this->request->get['route'] == 'product/category') && isset($this->request->get['path']) && $this->config->get('information_optimblog_canonical_category_product') && $this->config->get('information_optimblog_status')) {
			$this->load->model('extension/information/optimblog_category');

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$this->request->get['path'] = $this->model_extension_information_optimblog_category->getCategoryPath($category_id);

			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				if (!empty($this->request->get['filter'])) {
					$filter = $this->request->get['filter'];
				} else {
					$filter = '';
				}

				if (!empty($this->request->get['sort'])) {
					$sort = $this->request->get['sort'];
				} else {
					$sort = 'p.sort_order';
				}

				if (!empty($this->request->get['order'])) {
					$order = $this->request->get['order'];
				} else {
					$order = 'ASC';
				}

				if (!empty($this->request->get['page'])) {
					$page = $this->request->get['page'];
				} else {
					$page = 1;
				}

				if (!empty($this->request->get['limit'])) {
					$limit = (int)$this->request->get['limit'];
				} else {
					$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
				}

				$filter_data = array(
					'filter_category_id' => $category_id,
					'filter_filter'      => $filter,
					'sort'               => $sort,
					'order'              => $order,
					'start'              => ($page - 1) * $limit,
					'limit'              => $limit
				);

				$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

				$links = $data['links'];

				$data['links'] = array();

				foreach ($links as $link) {
					if ($link['rel'] != 'canonical' && $link['rel'] != 'prev' && $link['rel'] != 'next') {
						$data['links'][$link['href']] = array(
							'href' => $link['href'],
							'rel'  => $link['rel']
						);
					}
				}

				if ($page == 1) {
					$data['links'][$this->url->link('product/category', 'path=' . $this->request->get['path'])] = array(
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path']),
						'rel'  => 'canonical'
					);
				} else {
					$data['links'][$this->url->link('product/category', 'path=' . $this->request->get['path'] . '&page='. $page)] = array(
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&page='. $page),
						'rel'  => 'canonical'
					);
				}
			
				if ($page > 1) {
					$data['links'][$this->url->link('product/category', 'path=' . $this->request->get['path'] . (($page - 2) ? '&page='. ($page - 1) : ''))] = array(
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . (($page - 2) ? '&page='. ($page - 1) : '')),
						'rel'  => 'prev'
					);
				}

				if ($limit && ceil($product_total / $limit) > $page) {
					$data['links'][$this->url->link('product/category', 'path=' . $this->request->get['path'] . '&page='. ($page + 1))] = array(
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&page='. ($page + 1)),
						'rel'  => 'next'
					);
				}
			}
		}

		// Canonical Information
		if ((isset($this->request->get['route']) && $this->request->get['route'] == 'information/information') && isset($this->request->get['information_id']) && $this->config->get('information_optimblog_status')) {
			$information_id = (int)$this->request->get['information_id'];

			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$this->load->model('extension/information/optimblog_information');

				$information_images = $this->model_extension_information_optimblog_information->getInformationImages($information_id);

				if ($information_images) {
					if (!empty($this->config->get('information_optimblog_information_style'))) {
						foreach ($this->config->get('information_optimblog_information_style') as $link) {
							$data['styles'][$link] = array(
								'href'  => $link,
								'rel'   => 'stylesheet',
								'media' => 'screen'
							);
						}
					}

					if (!empty($this->config->get('information_optimblog_information_script')['header'])) {
						foreach ($this->config->get('information_optimblog_information_script')['header'] as $link) {
							$data['scripts'][] = $link;
						}
					}
				}

				// Canonical Information
				$data['links'][$this->url->link('information/information', 'information_id=' . $information_id)] = array(
					'href' => $this->url->link('information/information', 'information_id=' . $information_id),
					'rel'  => 'canonical'
				);

				if ($this->config->get('information_optimblog_canonical_information')) {
					$this->load->model('extension/information/optimblog_category');

					$main_category = $this->model_extension_information_optimblog_information->getMainCategory($information_id);

					if ($main_category) {
						$category_path = $this->model_extension_information_optimblog_category->getCategoryPath($main_category);

						if ($category_path) {
							$links = $data['links'];

							$data['links'] = array();

							foreach ($links as $link) {
								if ($link['rel'] != 'canonical') {
									$data['links'][$link['href']] = array(
										'href' => $link['href'],
										'rel'  => $link['rel']
									);
								}
							}

							$data['links'][$this->url->link('information/information', 'path=' . $category_path . '&information_id=' . $information_id)] = array(
								'href' => $this->url->link('information/information', 'path=' . $category_path . '&information_id=' . $information_id),
								'rel'  => 'canonical'
							);
						}
					}
				}
			}
		}
	}

	// view/common/footer/before
	public function viewFooterBefore(&$route, &$data) {
		if ($this->config->get('information_optimblog_status')) {
			// Footer Scripts
			if (!empty($this->config->get('information_optimblog_information_script')['footer'])) {
				foreach ($this->config->get('information_optimblog_information_script')['footer'] as $link) {
					$data['scripts'][] = $link;
				}
			}
		}
	}

	// view/product/category/before
	public function viewCategory(&$route, &$data) {
		if (isset($this->request->get['path']) && $this->config->get('information_optimblog_status')) {
			$this->load->model('extension/information/optimblog_category');

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$category_info = $this->model_catalog_category->getCategory($category_id);

			$data['images'] = array();

			$results = $this->model_extension_information_optimblog_category->getCategoryImages($category_id);

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('information_optimblog_image_category_popup_width'), $this->config->get('information_optimblog_image_category_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('information_optimblog_image_category_additional_width'), $this->config->get('information_optimblog_image_category_additional_height'))
				);
			}

			$data['heading_title'] = $category_info['header'] ? $category_info['header'] : $category_info['name'];

			// Breadcrumbs Category Product
			if ($this->config->get('information_optimblog_breadcrumbs_category_product')) {
				$data['breadcrumbs'] = array();

				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_home'),
					'href' => $this->url->link('common/home')
				);

				$url = '';

				if (!empty($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (!empty($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (!empty($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$path = '';

				$category_path = $this->model_extension_information_optimblog_category->getCategoryPath($category_id);
            
				$this->request->get['path'] = $category_path;
            
				$parts = explode('_', (string)$this->request->get['path']);

				(int)array_pop($parts);
        
				foreach ($parts as $path_id) {
					if (!$path) {
						$path = (int)$path_id;
					} else {
						$path .= '_' . (int)$path_id;
					}

					$category_path_info = $this->model_catalog_category->getCategory($path_id);

					if ($category_path_info) {
						$data['breadcrumbs'][] = array(
							'text' => $category_path_info['name'],
							'href' => $this->url->link('product/category', 'path=' . $path . $url)
						);
					}
				}

				if ($category_info) {
					// Set the last category breadcrumb
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
					);
				}
			}
		}
	}

	// view/product/product/before
	public function viewProduct(&$route, &$data) {
		if (isset($this->request->get['product_id']) && $this->config->get('information_optimblog_status')) {
			$product_id = (int)$this->request->get['product_id'];

			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				// Breadcrumbs Product
				$main_category = $this->model_extension_information_optimblog_product->getMainCategory($product_id);

				if ($main_category && !isset($this->request->get['path']) && !isset($this->request->get['manufacturer_id']) && !isset($this->request->get['search']) && !isset($this->request->get['tag']) && $this->config->get('information_optimblog_breadcrumbs_product')) {
					$this->load->model('extension/information/optimblog_category');

					$data['breadcrumbs'] = array();

					$data['breadcrumbs'][] = array(
						'text' => $this->language->get('text_home'),
						'href' => $this->url->link('common/home')
					);

					$this->request->get['path'] = $this->model_extension_information_optimblog_category->getCategoryPath($main_category);

					$path = '';

					$parts = explode('_', (string)$this->request->get['path']);

					(int)array_pop($parts);
      
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
								'href' => $this->url->link('product/category', 'path=' . $path)
							);
						}
					}

					// Set the last category breadcrumb
					$category_info = $this->model_catalog_category->getCategory($main_category);

					if ($category_info) {
						$url = '';

						if (!empty($this->request->get['sort'])) {
							$url .= '&sort=' . $this->request->get['sort'];
						}

						if (!empty($this->request->get['order'])) {
							$url .= '&order=' . $this->request->get['order'];
						}

						if (!empty($this->request->get['page'])) {
							$url .= '&page=' . $this->request->get['page'];
						}

						if (!empty($this->request->get['limit'])) {
							$url .= '&limit=' . $this->request->get['limit'];
						}

						$data['breadcrumbs'][] = array(
							'text' => $category_info['name'],
							'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
						);
					}

					$url = '';

					if (!empty($this->request->get['path'])) {
						$url .= '&path=' . $this->request->get['path'];
					}

					if (!empty($this->request->get['filter'])) {
						$url .= '&filter=' . $this->request->get['filter'];
					}

					if (!empty($this->request->get['description'])) {
						$url .= '&description=' . $this->request->get['description'];
					}

					if (!empty($this->request->get['category_id'])) {
						$url .= '&category_id=' . $this->request->get['category_id'];
					}

					if (!empty($this->request->get['sub_category'])) {
						$url .= '&sub_category=' . $this->request->get['sub_category'];
					}

					if (!empty($this->request->get['sort'])) {
						$url .= '&sort=' . $this->request->get['sort'];
					}

					if (!empty($this->request->get['order'])) {
						$url .= '&order=' . $this->request->get['order'];
					}

					if (!empty($this->request->get['page'])) {
						$url .= '&page=' . $this->request->get['page'];
					}

					if (!empty($this->request->get['limit'])) {
						$url .= '&limit=' . $this->request->get['limit'];
					}

					$data['breadcrumbs'][] = array(
						'text' => $product_info['name'],
						'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
					);
				}

				$data['heading_title'] = $product_info['header'] ? $product_info['header'] : $product_info['name'];
				$data['short_description'] = html_entity_decode($product_info['short_description'], ENT_QUOTES, 'UTF-8');

				$data['informations'] = array();

				$results = $this->model_extension_information_optimblog_product->getInformationRelated($product_id);

				foreach ($results as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('information_optimblog_image_related_width'), $this->config->get('information_optimblog_image_related_height'));
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
						'reviews'        => sprintf($this->language->get('text_related_reviews'), $result['reviews']),
						'rating'         => $result['rating'],
						'href'           => $this->url->link('information/information', 'information_id=' . $result['information_id'])
					);
				}

				$data['show_related_author'] = $this->config->get('information_optimblog_category_author');
				$data['show_related_date'] = $this->config->get('information_optimblog_category_date');
				$data['show_related_review'] = $this->config->get('information_optimblog_category_review');
			}
		}
	}

	// view/information/information/before
	public function viewInformationBefore(&$route, &$data) {
		if (!empty($this->request->get['information_id']) && $this->config->get('information_optimblog_status')) {
			$information_id = (int)$this->request->get['information_id'];

			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				// Breadcrumbs Information
				if ($this->config->get('information_optimblog_breadcrumbs_information')) {
					$data['breadcrumbs'] = array();

					$data['breadcrumbs'][] = array(
						'text' => $this->language->get('text_home'),
						'href' => $this->url->link('common/home')
					);

					if (!empty($this->request->get['path'])) {
						$path = '';

						$parts = explode('_', (string)$this->request->get['path']);

						$category_id = (int)array_pop($parts);

						foreach ($parts as $path_id) {
							if (!$path) {
								$path = $path_id;
							} else {
								$path .= '_' . $path_id;
							}

							$category_info = $this->model_catalog_category->getCategory($path_id);

							if ($category_info) {
								$data['breadcrumbs'][] = array(
									'text' => $category_info['name'],
									'href' => $this->url->link('product/category', 'path=' . $path)
								);
							}
						}

						// Set the last category breadcrumb
						$category_info = $this->model_catalog_category->getCategory($category_id);

						if ($category_info) {
							$url = '';

							if (!empty($this->request->get['sort'])) {
								$url .= '&sort=' . $this->request->get['sort'];
							}

							if (!empty($this->request->get['order'])) {
								$url .= '&order=' . $this->request->get['order'];
							}

							if (!empty($this->request->get['page'])) {
								$url .= '&page=' . $this->request->get['page'];
							}

							if (!empty($this->request->get['limit'])) {
								$url .= '&limit=' . $this->request->get['limit'];
							}

							$data['breadcrumbs'][] = array(
								'text' => $category_info['name'],
								'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
							);
						}
					}

					if (!empty($this->request->get['manufacturer_id'])) {
						$data['breadcrumbs'][] = array(
							'text' => $this->language->get('text_brand'),
							'href' => $this->url->link('product/manufacturer')
						);

						$url = '';

						if (!empty($this->request->get['sort'])) {
							$url .= '&sort=' . $this->request->get['sort'];
						}

						if (!empty($this->request->get['order'])) {
							$url .= '&order=' . $this->request->get['order'];
						}

						if (!empty($this->request->get['page'])) {
							$url .= '&page=' . $this->request->get['page'];
						}

						if (!empty($this->request->get['limit'])) {
							$url .= '&limit=' . $this->request->get['limit'];
						}

						$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

						if ($manufacturer_info) {
							$data['breadcrumbs'][] = array(
								'text' => $manufacturer_info['name'],
								'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
							);
						}
					}

					if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
						$url = '';

						if (!empty($this->request->get['search'])) {
							$url .= '&search=' . $this->request->get['search'];
						}

						if (!empty($this->request->get['tag'])) {
							$url .= '&tag=' . $this->request->get['tag'];
						}

						if (!empty($this->request->get['description'])) {
							$url .= '&description=' . $this->request->get['description'];
						}

						if (!empty($this->request->get['category_id'])) {
							$url .= '&category_id=' . $this->request->get['category_id'];
						}

						if (!empty($this->request->get['sub_category'])) {
							$url .= '&sub_category=' . $this->request->get['sub_category'];
						}

						if (!empty($this->request->get['sort'])) {
							$url .= '&sort=' . $this->request->get['sort'];
						}

						if (!empty($this->request->get['order'])) {
							$url .= '&order=' . $this->request->get['order'];
						}

						if (!empty($this->request->get['page'])) {
							$url .= '&page=' . $this->request->get['page'];
						}

						if (!empty($this->request->get['limit'])) {
							$url .= '&limit=' . $this->request->get['limit'];
						}

						$data['breadcrumbs'][] = array(
							'text' => $this->language->get('text_search'),
							'href' => $this->url->link('information/search', $url)
						);
					}
        
					$this->load->model('extension/information/optimblog_category');

					$main_category = $this->model_extension_information_optimblog_information->getMainCategory($information_id);

					if (!isset($this->request->get['path']) && !isset($this->request->get['manufacturer_id']) && !isset($this->request->get['search']) && !isset($this->request->get['tag']) && $main_category) {
						$this->request->get['path'] = $this->model_extension_information_optimblog_category->getCategoryPath($main_category);

						$path = '';

						$parts = explode('_', (string)$this->request->get['path']);

						(int)array_pop($parts);
      
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
									'href' => $this->url->link('product/category', 'path=' . $path)
								);
							}
						}

						// Set the last category breadcrumb
						$category_info = $this->model_catalog_category->getCategory($main_category);

						if ($category_info) {
							$url = '';

							if (!empty($this->request->get['sort'])) {
								$url .= '&sort=' . $this->request->get['sort'];
							}

							if (!empty($this->request->get['order'])) {
								$url .= '&order=' . $this->request->get['order'];
							}

							if (!empty($this->request->get['page'])) {
								$url .= '&page=' . $this->request->get['page'];
							}

							if (!empty($this->request->get['limit'])) {
								$url .= '&limit=' . $this->request->get['limit'];
							}

							$data['breadcrumbs'][] = array(
								'text' => $category_info['name'],
								'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
							);
						}
					}

					$url = '';

					if (!empty($this->request->get['path'])) {
						$url .= '&path=' . $this->request->get['path'];
					}

					if (!empty($this->request->get['filter'])) {
						$url .= '&filter=' . $this->request->get['filter'];
					}

					if (!empty($this->request->get['manufacturer_id'])) {
						$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
					}

					if (!empty($this->request->get['search'])) {
						$url .= '&search=' . $this->request->get['search'];
					}

					if (!empty($this->request->get['tag'])) {
						$url .= '&tag=' . $this->request->get['tag'];
					}

					if (!empty($this->request->get['description'])) {
						$url .= '&description=' . $this->request->get['description'];
					}

					if (!empty($this->request->get['category_id'])) {
						$url .= '&category_id=' . $this->request->get['category_id'];
					}

					if (!empty($this->request->get['sub_category'])) {
						$url .= '&sub_category=' . $this->request->get['sub_category'];
					}

					if (!empty($this->request->get['sort'])) {
						$url .= '&sort=' . $this->request->get['sort'];
					}

					if (!empty($this->request->get['order'])) {
						$url .= '&order=' . $this->request->get['order'];
					}

					if (!empty($this->request->get['page'])) {
						$url .= '&page=' . $this->request->get['page'];
					}

					if (!empty($this->request->get['limit'])) {
						$url .= '&limit=' . $this->request->get['limit'];
					}

					$data['breadcrumbs'][] = array(
						'text' => $information_info['title'],
						'href' => $this->url->link('information/information', $url . '&information_id=' .  $information_id)
					);
				}

				$data['heading_title'] = $information_info['header'] ? $information_info['header'] : $information_info['title'];

				$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

				$data['text_review'] = sprintf($this->language->get('text_review'), $information_info['reviews']);

				$data['information_id'] = $information_id;
				$data['author'] = $information_info['author'];
				$data['date_added'] = date($this->language->get('date_format_information'), strtotime($information_info['date_added']));
				$data['manufacturer'] = $information_info['manufacturer'];
				$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $information_info['manufacturer_id']);
				$data['short_description'] = html_entity_decode($information_info['short_description'], ENT_QUOTES, 'UTF-8');

				$this->load->model('tool/image');

				if ($information_info['image'] && $this->config->get('information_optimblog_information_thumb')) {
					$data['popup'] = $this->model_tool_image->resize($information_info['image'], $this->config->get('information_optimblog_image_popup_width'), $this->config->get('information_optimblog_image_popup_height'));
				} else {
					$data['popup'] = '';
				}

				if ($information_info['image'] && $this->config->get('information_optimblog_information_thumb')) {
					$data['thumb'] = $this->model_tool_image->resize($information_info['image'], $this->config->get('information_optimblog_image_thumb_width'), $this->config->get('information_optimblog_image_thumb_height'));
				} else {
					$data['thumb'] = '';
				}

				$data['images'] = array();

				$results = $this->model_extension_information_optimblog_information->getInformationImages($information_id);

				foreach ($results as $result) {
					$data['images'][] = array(
						'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('information_optimblog_image_popup_width'), $this->config->get('information_optimblog_image_popup_height')),
						'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('information_optimblog_image_additional_width'), $this->config->get('information_optimblog_image_additional_height'))
					);
				}

				$data['review_status'] = $this->config->get('information_optimblog_review_status');

				if ($this->config->get('information_optimblog_review_guest') || $this->customer->isLogged()) {
					$data['review_guest'] = true;
				} else {
					$data['review_guest'] = false;
				}

				if ($this->customer->isLogged()) {
					$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
				} else {
					$data['customer_name'] = '';
				}

				$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$information_info['reviews']);
				$data['rating'] = (int)$information_info['rating'];

				// Captcha
				if ($this->config->get('captcha_' . $this->config->get('information_optimblog_captcha') . '_status') && $this->config->get('information_optimblog_captcha')) {
					$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('information_optimblog_captcha'));
				} else {
					$data['captcha'] = '';
				}

				$data['share'] = trim(html_entity_decode($this->config->get('information_optimblog_share'), ENT_QUOTES, 'UTF-8'));

				$data['attribute_groups'] = $this->model_extension_information_optimblog_information->getInformationAttributes($information_id);

				$data['informations'] = array();

				$results = $this->model_extension_information_optimblog_information->getInformationRelated($information_id);

				foreach ($results as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('information_optimblog_image_related_width'), $this->config->get('information_optimblog_image_related_height'));
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
						'reviews'        => sprintf($this->language->get('text_related_reviews'), $result['reviews']),
						'rating'         => $result['rating'],
						'href'           => $this->url->link('information/information', 'information_id=' . $result['information_id'])
					);
				}

				$data['products'] = array();

				$results = $this->model_extension_information_optimblog_information->getProductRelated($information_id);

				foreach ($results as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$result['special']) {
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = (int)$result['rating'];
					} else {
						$rating = false;
					}

					$data['products'][] = array(
						'product_id'  => $result['product_id'],
						'thumb'       => $image,
						'name'        => $result['name'],
						'description' =>  !empty($result['short_description']) ? trim(html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8')) : utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
						'rating'      => $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
					);
				}

				$data['tags'] = array();

				if ($information_info['tag']) {
					$tags = explode(',', $information_info['tag']);

					foreach ($tags as $tag) {
						$data['tags'][] = array(
							'tag'  => trim($tag),
							'href' => $this->url->link('information/search', 'tag=' . trim($tag))
						);
					}
				}

				$this->model_extension_information_optimblog_information->updateViewed($information_id);

				if (!empty($this->config->get('information_optimblog_exclusion_information')) && in_array($information_id, $this->config->get('information_optimblog_exclusion_information'))) {
					$data['information_exclusion'] = true;
				} else {
					$data['information_exclusion'] = false;
				}

				if ($this->config->get('information_optimblog_information_author') && !empty($this->config->get('information_optimblog_exclusion_information_author')) && in_array($information_id, $this->config->get('information_optimblog_exclusion_information_author'))) {
					$data['show_author'] = false;
				} elseif (!$this->config->get('information_optimblog_information_author') && !empty($this->config->get('information_optimblog_exclusion_information_author')) && in_array($information_id, $this->config->get('information_optimblog_exclusion_information_author'))){
					$data['show_author'] = true;
				} else {
					$data['show_author'] = $this->config->get('information_optimblog_information_author');
				}

				if ($this->config->get('information_optimblog_information_date') && !empty($this->config->get('information_optimblog_exclusion_information_date')) && in_array($information_id, $this->config->get('information_optimblog_exclusion_information_date'))) {
					$data['show_date'] = false;
				} elseif (!$this->config->get('information_optimblog_information_date') && !empty($this->config->get('information_optimblog_exclusion_information_date')) && in_array($information_id, $this->config->get('information_optimblog_exclusion_information_date'))){
					$data['show_date'] = true;
				} else {
					$data['show_date'] = $this->config->get('information_optimblog_information_date');
				}

				if ($this->config->get('information_optimblog_information_manufacturer') && !empty($this->config->get('information_optimblog_exclusion_information_manufacturer')) && in_array($information_id, $this->config->get('information_optimblog_exclusion_information_manufacturer'))) {
					$data['show_manufacturer'] = false;
				} elseif (!$this->config->get('information_optimblog_information_manufacturer') && !empty($this->config->get('information_optimblog_exclusion_information_manufacturer')) && in_array($information_id, $this->config->get('information_optimblog_exclusion_information_manufacturer'))){
					$data['show_manufacturer'] = true;
				} else {
					$data['show_manufacturer'] = $this->config->get('information_optimblog_information_manufacturer');
				}

				if ($this->config->get('information_optimblog_information_review') && !empty($this->config->get('information_optimblog_exclusion_information_review')) && in_array($information_id, $this->config->get('information_optimblog_exclusion_information_review'))) {
					$data['show_review'] = false;
				} elseif (!$this->config->get('information_optimblog_information_review') && !empty($this->config->get('information_optimblog_exclusion_information_review')) && in_array($information_id, $this->config->get('information_optimblog_exclusion_information_review'))){
					$data['show_review'] = true;
				} else {
					$data['show_review'] = $this->config->get('information_optimblog_information_review');
				}

				$data['show_related_author'] = $this->config->get('information_optimblog_category_author');
				$data['show_related_date'] = $this->config->get('information_optimblog_category_date');
				$data['show_related_review'] = $this->config->get('information_optimblog_category_review');
			}
		}
	}

	// controller/information/information/review/before
	public function informationReview(&$route, &$data) {
		$this->load->language('information/information');

		$this->load->model('extension/information/optimblog_review');

		if (!empty($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_extension_information_optimblog_review->getTotalReviewsByInformationId($this->request->get['information_id']);

		$results = $this->model_extension_information_optimblog_review->getReviewsByInformationId($this->request->get['information_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'reply'      => html_entity_decode($result['reply'], ENT_QUOTES, 'UTF-8'),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('information/information/review', 'information_id=' . $this->request->get['information_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		$this->response->setOutput($this->load->view('product/review', $data));

		return true;
	}

	// controller/information/information/write/before
	public function informationWrite(&$route, &$data) {
		$this->load->language('information/information');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('information_optimblog_captcha') . '_status') && $this->config->get('information_optimblog_captcha')) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('information_optimblog_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('extension/information/optimblog_review');

				$this->model_extension_information_optimblog_review->addInformationReview($this->request->get['information_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

		return true;
	}

	// controller/information/information/before
//	public function informationBefore(&$route, &$args) {
//		$this->load->model('extension/information/optimblog_information');
//	}

	// model/catalog/information/getInformation/before
	public function getInformation(&$route, &$args) {
		$this->load->model('extension/information/optimblog_information');
		$route = 'extension/information/optimblog_information/getInformation';
	}

	// model/catalog/information/getInformations/before
	public function getInformations(&$route, &$args) {
		$this->load->model('extension/information/optimblog_information');
		$route = 'extension/information/optimblog_information/getInformations';
	}

	// model/catalog/product/getProduct/before
	public function getProduct(&$route, &$args) {
		$this->load->model('extension/information/optimblog_product');
		$route = 'extension/information/optimblog_product/getProduct';
	}

	// model/catalog/product/getProductRelated/before
	public function getProductRelated(&$route, &$args) {
		$this->load->model('extension/information/optimblog_product');
		$route = 'extension/information/optimblog_product/getProductRelated';
	}

	/**
	 * Unnecessary LEFT JOIN in getTotalReviewsByProductId and getReviewsByProductId
	 * https://github.com/opencart/opencart/issues/6656
	 */
	// model/catalog/review/getReviewsByProductId/before
	public function getReviewsByProductId(&$route, &$args) {
		$this->load->model('extension/information/optimblog_review');
		$route = 'extension/information/optimblog_review/getReviewsByProductId';
	}

	// model/catalog/review/getTotalReviewsByProductId/before
	public function getTotalReviewsByProductId(&$route, &$args) {
		$this->load->model('extension/information/optimblog_review');
		$route = 'extension/information/optimblog_review/getTotalReviewsByProductId';
	}
}