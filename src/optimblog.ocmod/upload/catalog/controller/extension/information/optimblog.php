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
}