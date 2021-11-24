<?php
/**
 * @package    OptimBlog
 * @version    3.0.1.4
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
class ModelExtensionInformationOptimBlogInformation extends Model {
	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT *, id.title AS title, i.image, m.name AS manufacturer, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.information_id = i.information_id AND r1.status = '1' GROUP BY r1.information_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.information_id = i.information_id AND r2.status = '1' GROUP BY r2.information_id) AS reviews, (SELECT u.username FROM " . DB_PREFIX . "user u WHERE u.user_id = i2u.user_id) AS author, i.sort_order FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (i.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "information_to_user i2u ON (i.information_id = i2u.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1' AND i.date_available <= NOW() AND i.date_end >= NOW() AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'information_id'    => $query->row['information_id'],
				'title'             => $query->row['title'],
				'header'            => $query->row['header'],
				'short_description' => $query->row['short_description'],
				'description'       => $query->row['description'],
				'meta_title'        => $query->row['meta_title'],
				'meta_description'  => $query->row['meta_description'],
				'meta_keyword'      => $query->row['meta_keyword'],
				'tag'               => $query->row['tag'],
				'image'             => $query->row['image'],
				'bottom'            => $query->row['bottom'],
				'manufacturer_id'   => $query->row['manufacturer_id'],
				'manufacturer'      => $query->row['manufacturer'],
				'rating'            => round($query->row['rating']),
				'reviews'           => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'user_id'           => $query->row['user_id'],
				'author'            => $query->row['author'],
				'sort_order'        => $query->row['sort_order'],
				'status'            => $query->row['status'],
				'date_available'    => $query->row['date_available'],
				'date_end'          => $query->row['date_end'],
				'date_added'        => $query->row['date_added'],
				'date_modified'     => $query->row['date_modified'],
				'viewed'            => $query->row['viewed']
			);
		} else {
			return false;
		}
	}

	public function getInformations($data = array()) {
		$sql = "SELECT i.information_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.information_id = i.information_id AND r1.status = '1' GROUP BY r1.information_id) AS rating";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "information_to_category i2c ON (cp.category_id = i2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "information_to_category i2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "information_filter if ON (i2c.information_id = if.information_id) LEFT JOIN " . DB_PREFIX . "information i ON (if.information_id = i.information_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "information i ON (i2c.information_id = i.information_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "information i";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1' AND i.date_available <= NOW() AND i.date_end >= NOW() AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND i2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND if.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "id.title LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR id.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "id.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND i.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$sql .= " GROUP BY i.information_id";

		$sort_data = array(
			'id.title',
			'rating',
			'i.viewed',
			'i.sort_order',
			'i.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'id.title') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY i.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(id.title) DESC";
		} else {
			$sql .= " ASC, LCASE(id.title) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$information_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$information_data[$result['information_id']] = $this->getInformation($result['information_id']);
		}

		return $information_data;
	}
   
	public function getTotalInformations($data = array()) {
		$sql = "SELECT COUNT(DISTINCT i.information_id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "information_to_category i2c ON (cp.category_id = i2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "information_to_category i2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "information_filter if ON (i2c.information_id = if.information_id) LEFT JOIN " . DB_PREFIX . "information i ON (if.information_id = i.information_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "information i ON (i2c.information_id = i.information_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "information i";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1' AND i.date_available <= NOW() AND i.date_end >= NOW() AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND i2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND if.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "id.title LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR id.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "id.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND i.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getInformationAttributes($information_id) {
		$information_attribute_group_data = array();

		$information_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "information_attribute ia LEFT JOIN " . DB_PREFIX . "attribute a ON (ia.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE ia.information_id = '" . (int)$information_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

		foreach ($information_attribute_group_query->rows as $information_attribute_group) {
			$information_attribute_data = array();

			$information_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, ia.text FROM " . DB_PREFIX . "information_attribute ia LEFT JOIN " . DB_PREFIX . "attribute a ON (ia.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ia.information_id = '" . (int)$information_id . "' AND a.attribute_group_id = '" . (int)$information_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ia.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

			foreach ($information_attribute_query->rows as $information_attribute) {
				$information_attribute_data[] = array(
					'attribute_id' => $information_attribute['attribute_id'],
					'name'         => $information_attribute['name'],
					'text'         => $information_attribute['text']
				);
			}

			$information_attribute_group_data[] = array(
				'attribute_group_id' => $information_attribute_group['attribute_group_id'],
				'name'               => $information_attribute_group['name'],
				'attribute'          => $information_attribute_data
			);
		}

		return $information_attribute_group_data;
	}

	public function getInformationImages($information_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_image WHERE information_id = '" . (int)$information_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getInformationRelated($information_id) {
		$information_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_related ir LEFT JOIN " . DB_PREFIX . "information i ON (ir.related_id = i.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE ir.information_id = '" . (int)$information_id . "' AND ir.route >= '0' AND i.status = '1' AND i.date_available <= NOW() AND i.date_end >= NOW() AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$information_data[$result['related_id']] = $this->getInformation($result['related_id']);
		}

		return $information_data;
	}

	public function getProductRelated($information_id) {
		$this->load->model('catalog/product');

		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_product_related ipr LEFT JOIN " . DB_PREFIX . "product p ON (ipr.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE ipr.information_id = '" . (int)$information_id . "' AND ipr.route >= '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getMainCategory($information_id) {
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "information_to_category WHERE information_id = '" . (int)$information_id . "' AND main_category = '1'");

		if ($query->row) {
			return $query->row['category_id'];
		} else {
			return '';
		}
	}

	public function updateViewed($information_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "information SET viewed = (viewed + 1) WHERE information_id = '" . (int)$information_id . "'");
	}
}