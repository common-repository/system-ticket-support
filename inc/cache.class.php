<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('STS_Cache')) {
	class STS_Cache {
		private static $_instance;
		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		private $_global_cache = array();

		/**
		 * Set cache value
		 *
		 * @param $key
		 * @param $value
		 */
		public function set($key, $value) {
			$this->_global_cache[$key] = $value;
		}

		/**
		 * Get cache value
		 *
		 * @param $key
		 * @param null $default
		 *
		 * @return mixed|null
		 */
		public function get($key, $default = null) {
			return isset($this->_global_cache[$key]) ? $this->_global_cache[$key] : $default;
		}

		/**
		 * Check cache key exists
		 *
		 * @param $key
		 *
		 * @return bool
		 */
		public function exists($key) {
			return isset($this->_global_cache[$key]);
		}

		/**
		 * Flush cache
		 */
		public function clear() {
			$this->_global_cache = array();
		}
	}
}