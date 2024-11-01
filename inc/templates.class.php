<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('STS_Templates')) {
	class STS_Templates {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {
			add_action('admin_bar_init', array($this, 'off_admin_bar'));
			add_filter( 'template_include', array( $this, 'support_panel_template'));
		}

		public function off_admin_bar() {
			if (!sts_is_support_page()) {
				return;
			}

			if (function_exists('_admin_bar_bump_cb')) {
				remove_action( 'wp_head', '_admin_bar_bump_cb', 10 );
			}
			show_admin_bar(false);
		}

		public function support_panel_template($template) {
			if (!sts_is_support_page()) {
				return $template;
			}
			return STS()->locate_template('support-panel.php');
		}
	}
}