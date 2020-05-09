<?php
/**
 * @package    OptimBlog
 * @version    3.0.1.0
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
class ControllerExtensionInformationOptimBlog extends Controller {
	// catalog/language/product/category/after
	public function languageCategory(&$route) {
		$this->load->language('extension/information/optimblog_category');
	}

	// catalog/language/product/product/after
	public function languageProduct(&$route) {
		$this->load->language('extension/information/optimblog_product');
	}

	// catalog/language/information/information/after
	public function languageInformation(&$route) {
		$this->load->language('extension/information/optimblog_information');
	}

	// catalog/language/mail/review/after
	public function languageReview(&$route) {
		$this->load->language('extension/information/optimblog_review');
	}

	// catalog/controller/product/category/before
	public function controllerCategoryType(&$route, &$data) {
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$this->load->model('catalog/category');

			$category_info = $this->model_catalog_category->getCategory($category_id);

			if (!empty($category_info['information'])) {
				$route = 'information/category';
				$this->request->get['route'] = 'information/category';
			}
		}
	}

	// catalog/view/common/header/before
	public function canonical(&$route, &$data, &$template) {
		if ($this->request->get['route'] == 'product/category' && isset($this->request->get['path']) && $this->config->get('information_optimblog_canonical_category_product')) {
			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$this->request->get['path'] = $this->model_catalog_category->getCategoryPath($category_id);

			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				if (isset($this->request->get['filter'])) {
					$filter = $this->request->get['filter'];
				} else {
					$filter = '';
				}

				if (isset($this->request->get['sort'])) {
					$sort = $this->request->get['sort'];
				} else {
					$sort = 'p.sort_order';
				}

				if (isset($this->request->get['order'])) {
					$order = $this->request->get['order'];
				} else {
					$order = 'ASC';
				}

				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
				} else {
					$page = 1;
				}

				if (isset($this->request->get['limit'])) {
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

		if ($this->request->get['route'] == 'information/information' && isset($this->request->get['information_id']) && $this->config->get('information_optimblog_canonical_information')) {
			$information_id = (int)$this->request->get['information_id'];

			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$main_category = $this->model_catalog_information->getMainCategory($information_id);

				if ($main_category) {
					$category_path = $this->model_catalog_category->getCategoryPath($main_category);

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

	// catalog/view/product/category/before
	public function breadcrumbsCategoryProduct(&$route, &$data, &$template) {
		if (isset($this->request->get['path'])) {
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

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

			$category_path = $this->model_catalog_category->getCategoryPath($category_id);
            
			$this->request->get['path'] = $category_path;
            
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
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					);
				}
			}

			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				// Set the last category breadcrumb
				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
				);
			}
		}
	}

	// catalog/view/product/product/before
	public function breadcrumbsProduct(&$route, &$data, &$template) {
		if (isset($this->request->get['product_id']) && !isset($this->request->get['path']) && !isset($this->request->get['manufacturer_id']) && !isset($this->request->get['search']) && !isset($this->request->get['tag'])) {
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$product_id = (int)$this->request->get['product_id'];

			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$main_category = $this->model_catalog_product->getMainCategory($product_id);

				if ($main_category) {
					$this->request->get['path'] = $this->model_catalog_category->getCategoryPath($main_category);

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
							'text' => $category_info['name'],
							'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
						);
					}
				}

				$url = '';

				if (isset($this->request->get['path'])) {
					$url .= '&path=' . $this->request->get['path'];
				}

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['manufacturer_id'])) {
					$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
				}

				if (isset($this->request->get['search'])) {
					$url .= '&search=' . $this->request->get['search'];
				}

				if (isset($this->request->get['tag'])) {
					$url .= '&tag=' . $this->request->get['tag'];
				}

				if (isset($this->request->get['description'])) {
					$url .= '&description=' . $this->request->get['description'];
				}

				if (isset($this->request->get['category_id'])) {
					$url .= '&category_id=' . $this->request->get['category_id'];
				}

				if (isset($this->request->get['sub_category'])) {
					$url .= '&sub_category=' . $this->request->get['sub_category'];
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
					'text' => $product_info['name'],
					'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
				);
			}
		}
	}

	// catalog/view/information/information/before
	public function breadcrumbsInformation(&$route, &$data, &$template) {
		if (isset($this->request->get['information_id']) && !isset($this->request->get['path']) && !isset($this->request->get['manufacturer_id']) && !isset($this->request->get['search']) && !isset($this->request->get['tag'])) {
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$information_id = (int)$this->request->get['information_id'];

			$information_info = $this->model_catalog_information->getInformation($information_id);

			if ($information_info) {
				$main_category = $this->model_catalog_information->getMainCategory($information_id);

				if ($main_category) {
					$this->request->get['path'] = $this->model_catalog_category->getCategoryPath($main_category);

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
							'text' => $category_info['name'],
							'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
						);
					}
				}

				$url = '';

				if (isset($this->request->get['path'])) {
					$url .= '&path=' . $this->request->get['path'];
				}

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['manufacturer_id'])) {
					$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
				}

				if (isset($this->request->get['search'])) {
					$url .= '&search=' . $this->request->get['search'];
				}

				if (isset($this->request->get['tag'])) {
					$url .= '&tag=' . $this->request->get['tag'];
				}

				if (isset($this->request->get['description'])) {
					$url .= '&description=' . $this->request->get['description'];
				}

				if (isset($this->request->get['category_id'])) {
					$url .= '&category_id=' . $this->request->get['category_id'];
				}

				if (isset($this->request->get['sub_category'])) {
					$url .= '&sub_category=' . $this->request->get['sub_category'];
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
					'text' => $information_info['title'],
					'href' => $this->url->link('information/information', $url . '&information_id=' .  $information_id)
				);
			}
		}
	}
}