<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Notice' ) ) {
	class STS_Notice {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Set cache value
		 *
		 * @param $key
		 * @param $value
		 */
		public function set( $args ) {
			STS()->cache()->set( 'sts-notice', $args );

		}

		/**
		 * Get cache value
		 *
		 * @param $key
		 * @param null $default
		 *
		 * @return mixed|null
		 */
		public function display_notice() {
			if ( $this->exists( 'sts-notice' ) ) {
				return STS()->cache()->get( 'sts-notice' );
			}

			return '';

		}

		/**
		 * Check cache key exists
		 *
		 * @param $key
		 *
		 * @return bool
		 */
		public function exists( $key ) {
			return STS()->cache()->exists( $key );
		}

		/**
		 * Flush cache
		 */
		public function clear() {
			STS()->cache()->clear();
		}
	}
}