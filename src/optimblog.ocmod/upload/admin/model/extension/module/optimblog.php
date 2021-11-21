<?php
/**
 * @package    OptimBlog
 * @version    3.0.1.4
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
class ModelExtensionInformationOptimBlog extends Model {
	public function createTables() {
		// Category Image
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "category_image` (
				`category_image_id` int(11) NOT NULL AUTO_INCREMENT,
				`category_id` int(11) NOT NULL,
				`image` varchar(255) DEFAULT NULL,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				PRIMARY KEY (`category_image_id`),
				KEY `category_id` (`category_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		// Category Type
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "category' AND COLUMN_NAME = 'information'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category` ADD `information` tinyint(1) NOT NULL AFTER `date_modified`");
		}

		// Category Header
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "category_description' AND COLUMN_NAME = 'meta_h1'");

		if ($query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category_description` CHANGE `meta_h1` `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "category_description' AND COLUMN_NAME = 'meta_header'");

		if ($query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category_description` CHANGE `meta_header` `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "category_description' AND COLUMN_NAME = 'header'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category_description` ADD `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `name`");
		}

		// Category Short Description
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "category_description' AND COLUMN_NAME = 'short_description'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category_description` ADD `short_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `header`");
		}

		// Category Description Upgrade
		$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category_description`");

		foreach ($field_query->rows as $field) {
			if ($field['Field'] == 'header' && $field_name != 'name') {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "category_description` CHANGE `header` `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `name`");
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "category_description` CHANGE `short_description` `short_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `header`");
			}

			$field_name = $field['Field'];
		}

		// Information Image
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'image'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD `image` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL AFTER `information_id`");
		}

		// Information Manufacturer
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'manufacturer_id'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD `manufacturer_id` int(11) NOT NULL AFTER `image`");
		}

		// Information Top Menu
//		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'top'");
        
//		if (!$query->num_rows) {
//			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD `top` tinyint(1) NOT NULL DEFAULT '0' AFTER `manufacturer_id`");
//		}

		// Information Sort Order Type
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'sort_order' AND COLUMN_TYPE = 'int(11)'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` CHANGE `sort_order` `sort_order` INT(11) NOT NULL DEFAULT '0'");
		}

		// Information Viewed
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'viewed'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD `viewed` int(5) NOT NULL DEFAULT '0' AFTER `status`");
		}

		// Information Date Available
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'date_available'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD `date_available` datetime NOT NULL DEFAULT '2018-01-01 00:00:00' AFTER `viewed`");
		}

		// Information Date End
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'date_end'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD `date_end` datetime NOT NULL DEFAULT '2100-01-01 00:00:00' AFTER `date_available`");
		}

		// Information Date Added
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'date_added'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD `date_added` datetime NOT NULL AFTER `date_end`");
		}
        
		// Information Date Modified
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'date_modified'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD `date_modified` datetime NOT NULL AFTER `date_added`");
		}

		// Information UPDATE Date Added and Date Modified
		$this->db->query("UPDATE `" . DB_PREFIX . "information` SET `date_added` = NOW() WHERE `date_added` = 0");
		$this->db->query("UPDATE `" . DB_PREFIX . "information` SET `date_modified` = NOW() WHERE `date_modified` = 0");

		// Information Attribute
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "information_attribute` (
				`information_id` int(11) NOT NULL,
				`attribute_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`text` text NOT NULL,
				PRIMARY KEY (`information_id`, `attribute_id`, `language_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		// Information Header
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information_description' AND COLUMN_NAME = 'meta_h1'");

		if ($query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_description` CHANGE `meta_h1` `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information_description' AND COLUMN_NAME = 'meta_header'");

		if ($query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_description` CHANGE `meta_header` `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information_description' AND COLUMN_NAME = 'header'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_description` ADD `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `title`");
		}

		// Information Short Description
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information_description' AND COLUMN_NAME = 'short_description'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_description` ADD `short_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `header`");
		}

		// Information Description Type
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information_description' AND COLUMN_NAME = 'description' AND COLUMN_TYPE = 'text'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_description` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		}

		// Information Tag
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information_description' AND COLUMN_NAME = 'tag'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_description` ADD `tag` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `description`");
		}

		// Information Description Upgrade
		$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "information_description`");

		foreach ($field_query->rows as $field) {
			if ($field['Field'] == 'header' && $field_name != 'title') {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_description` CHANGE `header` `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `title`");
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_description` CHANGE `short_description` `short_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `header`");
                $this->db->query("ALTER TABLE `" . DB_PREFIX . "information_description` CHANGE `tag` `tag` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `description`");
			}

			$field_name = $field['Field'];
		}

		// Information Filter
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "information_filter` (
				`information_id` int(11) NOT NULL,
				`filter_id` int(11) NOT NULL,
				PRIMARY KEY (`information_id`, `filter_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		// Information Images
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "information_image` (
				`information_image_id` int(11) NOT NULL AUTO_INCREMENT,
				`information_id` int(11) NOT NULL,
				`image` varchar(255) DEFAULT NULL,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				PRIMARY KEY (`information_image_id`),
				KEY `information_id` (`information_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		// Information File
//		$this->db->query("
//			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "information_file` (
//				`information_file_id` int(11) NOT NULL AUTO_INCREMENT,
//                `information_id` int(11) NOT NULL,
//                `file` varchar(255) DEFAULT NULL,
//                `sort_order` int(3) NOT NULL DEFAULT '0',
//				PRIMARY KEY (`information_file_id`),
//                KEY `information_id` (`information_id`)
//			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
//		");

		// Information Related
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "information_related` (
				`information_id` int(11) NOT NULL,
                `related_id` int(11) NOT NULL,
                `route` tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (`information_id`, `related_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		// Information Product Related
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "information_product_related` (
				`information_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `route` tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (`information_id`, `product_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		// Information To Category
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "information_to_category` (
				`information_id` int(11) NOT NULL,
                `category_id` int(11) NOT NULL,
                `main_category` tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (`information_id`, `category_id`),
                KEY `category_id` (`category_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		// Information Customer Groups
//		$this->db->query("
//			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "information_to_customer_group` (
//				`information_id` int(11) NOT NULL,
//				`customer_group_id` int(11) NOT NULL,
//				PRIMARY KEY (`information_id`,`customer_group_id`)
//			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
//		");

		// Information User
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "information_to_user` (
				`information_id` int(11) NOT NULL,
				`user_id` int(11) NOT NULL,
				PRIMARY KEY (`information_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "information_to_user`");

		if (!$query->num_rows) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "information`");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "information_to_user` SET `information_id` = '" . (int)$result['information_id'] . "', `user_id` = '" . (int)$this->user->getId() . "'");
			}
		} else {
			$this->db->query("UPDATE `" . DB_PREFIX . "information_to_user` SET `user_id` = '" . (int)$this->user->getId() . "' WHERE `user_id` = ''");
		}

		// Information Review
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "review' AND COLUMN_NAME = 'information_id'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD `information_id` int(11) NOT NULL AFTER `product_id`");

			$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD KEY `information_id` (`information_id`)");
		}

		// Information Search
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'information/search' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET `store_id` = '" . (int)$this->config->get('config_store_id') . "', `language_id` = '" . (int)$this->config->get('config_language_id') . "', `query` = 'information/search', keyword = 'search-information'");
		}

		// Product Header
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_description' AND COLUMN_NAME = 'meta_h1'");

		if ($query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_description` CHANGE `meta_h1` `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_description' AND COLUMN_NAME = 'meta_header'");

		if ($query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_description` CHANGE `meta_header` `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_description' AND COLUMN_NAME = 'header'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_description` ADD `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `name`");
		}

		// Product Short Description
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_description' AND COLUMN_NAME = 'short_description'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_description` ADD `short_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `header`");
		}

		// Product Description Upgrade
		$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_description`");

		foreach ($field_query->rows as $field) {
			if ($field['Field'] == 'header' && $field_name != 'name') {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_description` CHANGE `header` `header` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `name`");
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_description` CHANGE `short_description` `short_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `header`");
			}

			$field_name = $field['Field'];
		}

		// Product Main Category
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_to_category' AND COLUMN_NAME = 'main_category'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_to_category` ADD `main_category` tinyint(1) NOT NULL DEFAULT '0'");
		}

		// Product Related
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_related' AND COLUMN_NAME = 'route'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_related` ADD `route` tinyint(1) NOT NULL DEFAULT '0'");
		}

		// Review Reply
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "review' AND COLUMN_NAME = 'reply'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD `reply` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `text`");
		}

		// Review Upgrade
		$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review`");

		foreach ($field_query->rows as $field) {
			if ($field['Field'] == 'information_id' && $field_name != 'product_id') {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` CHANGE `information_id` `information_id` int(11) NOT NULL AFTER `product_id`");
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` CHANGE `reply` `reply` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `text`");
			}

			$field_name = $field['Field'];
		}
        
		$key_information_id = false;

		$query = $this->db->query("SHOW INDEXES FROM `" . DB_PREFIX . "review`");
        
		foreach ($query->rows as $result) {
			if ($result['Key_name'] == 'information_id' && $result['Column_name'] == 'information_id') {
				$key_information_id = true;
			}
		}

		if (!$key_information_id) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD KEY `information_id` (`information_id`)");
		}

		// Customer Search
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "customer_search' AND COLUMN_NAME = 'informations'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_search` ADD `informations` INT(11) NOT NULL AFTER `products`");
		}

		// Customer Search Upgrade
		$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer_search`");

		foreach ($field_query->rows as $field) {
			if ($field['Field'] == 'informations' && $field_name != 'products') {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_search` CHANGE `informations` `informations` INT(11) NOT NULL AFTER `products`");
			}

			$field_name = $field['Field'];
		}
        
		// MyISAM Upgrade
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "category_image` ENGINE=MyISAM");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_attribute` ENGINE=MyISAM");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_filter` ENGINE=MyISAM");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_image` ENGINE=MyISAM");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_related` ENGINE=MyISAM");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_product_related` ENGINE=MyISAM");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_to_category` ENGINE=MyISAM");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "information_to_user` ENGINE=MyISAM");
	}

	public function update() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `code` = 'information'");

		if ($query->num_rows) {
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_status' WHERE `code` = 'information' AND `key` = 'information_setting_status'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_information_author' WHERE `code` = 'information' AND `key` = 'information_information_author'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_information_date' WHERE `code` = 'information' AND `key` = 'information_information_date'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_information_review' WHERE `code` = 'information' AND `key` = 'information_information_review'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_information_manufacturer' WHERE `code` = 'information' AND `key` = 'information_information_manufacturer'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_information_show' WHERE `code` = 'information' AND `key` = 'information_category_show'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_information_limit' WHERE `code` = 'information' AND `key` = 'information_limit'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_category_limit_show' WHERE `code` = 'information' AND `key` = 'information_category_limit_show'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_category_view' WHERE `code` = 'information' AND `key` = 'information_category_view'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_category_view_show' WHERE `code` = 'information' AND `key` = 'information_category_view_show'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_information_description_length' WHERE `code` = 'information' AND `key` = 'information_description_length'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_information_count' WHERE `code` = 'information' AND `key` = 'information_count'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_category_author' WHERE `code` = 'information' AND `key` = 'information_category_author'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_category_date' WHERE `code` = 'information' AND `key` = 'information_category_date'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_category_review' WHERE `code` = 'information' AND `key` = 'information_category_review'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_review_status' WHERE `code` = 'information' AND `key` = 'information_review_status'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_review_guest' WHERE `code` = 'information' AND `key` = 'information_review_guest'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_captcha' WHERE `code` = 'information' AND `key` = 'information_captcha'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_exclusion_information' WHERE `code` = 'information' AND `key` = 'information_exclusion'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_exclusion_information_author' WHERE `code` = 'information' AND `key` = 'information_exclusion_information_author'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_exclusion_information_date' WHERE `code` = 'information' AND `key` = 'information_exclusion_information_date'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_exclusion_information_review' WHERE `code` = 'information' AND `key` = 'information_exclusion_information_review'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_exclusion_information_manufacturer' WHERE `code` = 'information' AND `key` = 'information_exclusion_information_manufacturer'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_exclusion_category_author' WHERE `code` = 'information' AND `key` = 'information_exclusion_category_author'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_exclusion_category_date' WHERE `code` = 'information' AND `key` = 'information_exclusion_category_date'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_exclusion_category_review' WHERE `code` = 'information' AND `key` = 'information_exclusion_category_review'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_category_sort' WHERE `code` = 'information' AND `key` = 'information_category_sort'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_category_sort_show' WHERE `code` = 'information' AND `key` = 'information_category_sort_show'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_category_width'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_category_height'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_thumb_width'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_thumb_height'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_popup_width'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_popup_height'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_information_width' WHERE `code` = 'information' AND `key` = 'information_image_information_width'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_information_height'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_additional_width'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_additional_height'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_related_width'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_related_height'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_category_popup_width'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_category_popup_height'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_category_additional_width'");
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `code` = 'information_optimblog', `key` = 'information_optimblog_image_category_width' WHERE `code` = 'information' AND `key` = 'information_image_category_additional_height'");
		}
	}
}