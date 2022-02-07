<?php
/**
 * @package    OptimBlog
 * @version    3.1.0.1
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (https://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       https://optimcart.com
 */
class ModelExtensionModuleOptimBlogProduct extends Model {
	public function getProductRelated($product_id) {
		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND pr.route >= '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['related_id']] = $this->model_catalog_product->getProduct($result['related_id']);
		}

		return $product_data;
	}

	public function getInformationRelated($product_id) {
		$this->load->model('catalog/information');

		$information_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_product_related ipr LEFT JOIN " . DB_PREFIX . "information i ON (ipr.information_id = i.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE ipr.product_id = '" . (int)$product_id . "' AND ipr.route <= '0' AND i.status = '1' AND i.date_available <= NOW() AND i.date_end >= NOW() AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$information_data[$result['information_id']] = $this->model_catalog_information->getInformation($result['information_id']);
		}

		return $information_data;
	}

	public function getMainCategory($product_id) {
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND main_category = '1'");

		if ($query->row) {
			return $query->row['category_id'];
		} else {
			return '';
		}
	}
}