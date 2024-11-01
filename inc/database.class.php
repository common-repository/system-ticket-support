<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Database' ) ) {
	class STS_Database {
		private static $_instance;
		public $limit_user = 20;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * @return string
		 */
		public function table_prefix() {
			global $wpdb;

			return "{$wpdb->prefix}sts_";
		}

		/**
		 * @return STS_Database_Themes
		 */

		public function themes() {
			return STS_Database_Themes::getInstance();
		}
		/**
		 * @return STS_Database_Notification
		 */
		public function notification() {
			return STS_Database_Notification::getInstance();
		}

		/**
		 * @return STS_Database_Attachments
		 */
		public function attachments() {
			return STS_Database_Attachments::getInstance();
		}

		/**
		 * @return STS_Database_Tickets
		 */
		public function tickets() {
			return STS_Database_Tickets::getInstance();
		}

		/**
		 * @return STS_Database_Messages
		 */
		public function messages() {
			return STS_Database_Messages::getInstance();
		}

		/**
		 * @return STS_Database_Templates
		 */
		public function templates() {
			return STS_Database_Templates::getInstance();
		}

		/**
		 * @return STS_Database_Notes
		 */
		public function notes() {
			return STS_Database_Notes::getInstance();
		}

		/**
		 * @return STS_Database_Key_Mail_Rate
		 */
		public function key_mail_rate() {
			return STS_Database_Key_Mail_Rate::getInstance();
		}

		/**
		 * @return STS_Database_Mail_Template
		 */
		public function mail_template() {
			return STS_Database_Mail_Template::getInstance();
		}


	}
}