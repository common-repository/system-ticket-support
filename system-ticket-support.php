<?php
/**
 *  Plugin Name: System Ticket Support
 *  Plugin URI: http://plugins.svn.wordpress.org/system-ticket-support
 *  Description: The System Ticket Support plugin.
 *  Version: 1.0.0
 *  Author: G5Theme
 *  Author URI: http://g5plus.net
 *
 *  Text Domain: sts
 *  Domain Path: /languages/
 * License: GPLv2 or later
 *
 * @package G5
 * @category sts
 * @author G5
 *
 **/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'SystemTicketSupport' ) ):
	class SystemTicketSupport {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public $meta_prefix = 'sts_';

		public function __construct() {
			$this->init();
		}

		/**
		 * Init plugin
		 */
		public function init() {
			spl_autoload_register( array( $this, 'auto_load' ) );
			$this->includes();
			$this->install()->init();
			$this->endpoints()->init();
			$this->assets()->init();
			$this->templates()->init();
			$this->form_handler()->init();
			$this->ajax()->init();
			$this->cron_job()->init();
			$this->filter()->init();
			$this->action()->init();
			register_deactivation_hook( __FILE__, array( $this, 'ticket_cron_deactivation' ) );
			add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
		}

		/**
		 * Autoloader library class for plugin
		 *
		 * @param $class
		 */
		public function auto_load( $class ) {
			$file_name = preg_replace( '/^STS_/', '', $class );
			if ( $file_name !== $class ) {
				$path      = '';
				$file_name = strtolower( $file_name );
				$file_name = str_replace( '_', '-', $file_name );
				$this->load_file( $this->plugin_dir( "inc/{$path}{$file_name}.class.php" ) );
			}
		}

		/**
		 * Include library for plugin
		 */
		public function includes() {
			// Load function
			$this->load_file( $this->plugin_dir( 'inc/functions/helper.php' ) );
			$this->load_file( $this->plugin_dir( 'inc/functions/processing-register.php' ) );
			$this->load_file( $this->plugin_dir( 'inc/functions/upload-file.php' ) );
			$this->load_file( $this->plugin_dir( 'inc/functions/remove-theme-assets.php' ) );
			$this->load_file( $this->plugin_dir( 'inc/functions/set-form-login.php' ) );

			// Load admin modules
			$this->load_file( $this->plugin_dir( 'admin/admin.php' ) );
		}

		public function load_text_domain() {
			load_plugin_textdomain( 'sts', false, $this->plugin_dir( 'languages' ) );
		}

		/**
		 * Get plugin directory
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public function plugin_dir( $path = '' ) {
			return plugin_dir_path( __FILE__ ) . $path;
		}

		/**
		 * Get plugin url
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public function plugin_url( $path = '' ) {
			return trailingslashit( plugins_url( basename( __DIR__ ) ) ) . $path;
		}

		public function plugin_ver() {
			if ( STS()->cache()->exists( 'sts_plugin_version' ) ) {
				return STS()->cache()->get( 'sts_plugin_version' );
			}
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$plugin_data = get_plugin_data( __FILE__ );
			$plugin_ver  = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '1.0';

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) {
				$plugin_ver = uniqid( 'debug-' );
			}

			STS()->cache()->set( 'sts_plugin_version', $plugin_ver );

			return $plugin_ver;
		}

		/**
		 * Get plugin assets handler
		 *
		 * @param string $handle
		 *
		 * @return string
		 */
		public function assets_handle( $handle = '' ) {
			return "sts-{$handle}";
		}

		/**
		 * Get plugin assets url (CSS file or JS file)
		 *
		 * @param $file
		 *
		 * @return string
		 */
		public function asset_url( $file ) {
			if ( ! file_exists( $this->plugin_dir( $file ) ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
				$ext         = explode( '.', $file );
				$ext         = end( $ext );
				$normal_file = preg_replace( '/((\.min\.css)|(\.min\.js))$/', '', $file );
				if ( $normal_file != $file ) {
					$normal_file = untrailingslashit( $normal_file ) . ".{$ext}";
					if ( file_exists( $this->plugin_dir( $normal_file ) ) ) {
						return $this->plugin_url( untrailingslashit( $normal_file ) );
					}
				}
			}

			return $this->plugin_url( untrailingslashit( $file ) );
		}

		/**
		 * Include library for plugin
		 *
		 * @param $path
		 *
		 * @return bool
		 */
		public function load_file( $path ) {
			if ( $path && is_readable( $path ) ) {
				include_once $path;

				return true;
			}

			return false;
		}

		/**
		 * Locate template path from template name
		 *
		 * @param $template_name
		 * @param $args
		 *
		 * @return mixed|string|void
		 */
		public function locate_template( $template_name, $args = array() ) {
			$located = '';

			// Theme or child theme template
			$template = trailingslashit( get_stylesheet_directory() ) . 'support-ticket-system/' . $template_name;
			if ( file_exists( $template ) ) {
				$located = $template;
			}

			// Plugin template
			if ( ! $located ) {
				$located = $this->plugin_dir() . 'templates/' . $template_name;
			}
			$located = apply_filters( 'sts_locate_template', $located, $template_name, $args );

			// Return what we found.
			return $located;
		}

		/**
		 * Render template
		 *
		 * @param $template_name
		 * @param array $args
		 *
		 * @return mixed|string|void
		 */
		public function get_template( $template_name, $args = array() ) {
			if ( ! empty( $args ) && is_array( $args ) ) {
				extract( $args );
			}

			$located = $this->locate_template( $template_name, $args );
			if ( ! file_exists( $located ) ) {
				_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );

				return '';
			}

			do_action( 'sts_before_template_part', $template_name, $located, $args );
			include( $located );
			do_action( 'sts_after_template_part', $template_name, $located, $args );

			return $located;
		}

		/**
		 * Render plugin template
		 *
		 * @param $template_name
		 * @param array $args
		 *
		 * @return string
		 */
		public function get_plugin_template( $template_name, $args = array() ) {
			if ( $args && is_array( $args ) ) {
				extract( $args );
			}

			$located = $this->plugin_dir( $template_name );
			if ( ! file_exists( $located ) ) {
				_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_name ), '1.0' );

				return '';
			}

			do_action( 'sts_before_plugin_template', $template_name, $located, $args );
			include( $located );
			do_action( 'sts_after_plugin_template', $template_name, $located, $args );

			return $located;
		}

		/*--------------------------------------------------------------
		/* Plugin Object Instance
		--------------------------------------------------------------*/
		/**
		 * @return STS_Cache
		 */
		public function cache() {
			return STS_Cache::getInstance();
		}

		/**
		 * @return STS_Install
		 */
		public function install() {
			return STS_Install::getInstance();
		}

		/**
		 * @return STS_Endpoints
		 */
		public function endpoints() {
			return STS_Endpoints::getInstance();
		}

		/**
		 * @return STS_Assets
		 */
		public function assets() {
			return STS_Assets::getInstance();
		}

		/**
		 * @return STS_Templates
		 */
		public function templates() {
			return STS_Templates::getInstance();
		}

		/**
		 * @return STS_Form_Handler
		 */
		public function form_handler() {
			return STS_Form_Handler::getInstance();
		}

		/**
		 * @return STS_Ajax
		 */
		public function ajax() {
			return STS_Ajax::getInstance();
		}

		/**
		 * @return STS_Database
		 */
		public function db() {
			return STS_Database::getInstance();
		}

		/**
		 * @return STS_Cookie
		 */
		public function cookie() {
			return STS_Cookie::getInstance();
		}

		/**
		 * @return STS_Cron_Job
		 */
		public function cron_job() {
			return STS_Cron_Job::getInstance();
		}

		/**
		 * @return STS_Filter
		 */
		public function filter() {
			return STS_Filter::getInstance();
		}

		/**
		 * @return STS_Notice
		 */
		public function notice() {
			return STS_Notice::getInstance();
		}

		/**
		 * @return STS_Action
		 */
		public function action() {
			return STS_Action::getInstance();
		}

		function ticket_cron_deactivation() {
			wp_clear_scheduled_hook( 'task_unlock_reply_hook' );
			wp_clear_scheduled_hook( 'task_close_responded_ticket_hook' );
		}

	}

	function STS() {
		return SystemTicketSupport::getInstance();
	}

	STS();
endif;