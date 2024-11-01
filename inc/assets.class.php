<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Assets' ) ) {
	class STS_Assets {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {
			add_action( 'init', array( $this, 'register_assets' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 999 );
			add_filter( 'safe_style_css', array( $this, 'add_safe_css' ) );
		}

		public function register_assets() {
			// Deregister vendor style and script
			wp_deregister_style( array( 'esupport-google-fonts', 'font-awesome', 'bootstrap' ) );

			wp_register_style( 'font', 'https://fonts.googleapis.com/css?family=Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i' );
			// Ladda
			wp_register_style( 'ladda', STS()->asset_url( 'assets/vendors/ladda/ladda-themeless.min.css' ), array(), '1.0.5' );
			wp_register_script( 'ladda-spin', STS()->asset_url( 'assets/vendors/ladda/spin.min.js' ), array( 'jquery' ), '1.0.5', true );
			wp_register_script( 'ladda', STS()->asset_url( 'assets/vendors/ladda/ladda.min.js' ), array(
				'jquery',
				'ladda-spin'
			), '1.0.5', true );
			wp_register_script( 'ladda-jquery', STS()->asset_url( 'assets/vendors/ladda/ladda.jquery.min.js' ), array(
				'jquery',
				'ladda'
			), '1.0.5', true );

			// bootstrap
			wp_register_script( 'popper', STS()->asset_url( 'assets/vendors/popper/popper.min.js' ), array( 'jquery' ), '1.16.0', true );
			wp_register_style( 'bootstrap', STS()->asset_url( 'assets/vendors/bootstrap/css/bootstrap.min.css' ), array(), 'v4.4.1' );
			wp_register_script( 'bootstrap', STS()->asset_url( 'assets/vendors/bootstrap/js/bootstrap.min.js' ), array(
				'jquery',
				'popper'
			), 'v4.4.1', true );

			//Select2
			wp_register_style( 'select2', STS()->asset_url( 'assets/vendors/select2/dist/css/select2.min.css' ) );
			wp_register_script( 'select2', STS()->asset_url( 'assets/vendors/select2/dist/js/select2.full.min.js' ), array( 'jquery' ), '4.0.7', true );

			//datepicker
			wp_register_style( 'jquery-ui', STS()->asset_url( 'assets/vendors/jquery-ui/jquery-ui.min.css' ) );

			//magnific-popup
			wp_register_style( 'magnific-popup', STS()->asset_url( 'assets/vendors/magnific-popup/magnific-popup.css' ) );
			wp_register_script( 'magnific-popup', STS()->asset_url( 'assets/vendors/magnific-popup/jquery.magnific-popup.min.js' ), array( 'jquery' ), '1.1.0', true );

			//Chatjs
			wp_register_style( 'chatjs', STS()->asset_url( 'assets/vendors/chatjs/Chart.min.css' ) );
			wp_register_script( 'chatjs', STS()->asset_url( 'assets/vendors/chatjs/Chart.min.js' ), array( 'jquery' ), '2.8.0', true );

			//Jquery validate
			wp_register_script( 'jquery-validate', STS()->asset_url( 'assets/vendors/jquery-validation/jquery.validate.min.js' ), array( 'jquery' ), '1.19.0', true );
			wp_register_script( 'hc-sticky', STS()->asset_url( 'assets/vendors/hc-sticky/hc-sticky.min.js' ), array( 'jquery' ), '2.2.3', true );
			// Plugin assets
			wp_register_style( STS()->assets_handle( 'sts' ), STS()->asset_url( 'assets/css/sts.min.css' ), array(), STS()->plugin_ver() );
			wp_register_script( STS()->assets_handle( 'sts' ), STS()->asset_url( 'assets/js/sts-v2.js' ), array( 'jquery' ), STS()->plugin_ver(), true );
		}

		public function enqueue_assets() {
			if ( sts_is_support_page() ) {
				// Remove Theme Style
				wp_dequeue_style( apply_filters( 'sts_theme_current_style_handle', array() ) );

				// Remove Theme Script
				wp_dequeue_script( apply_filters( 'sts_theme_current_script_handle', array() ) );
				wp_enqueue_style( 'dashicons' );

				wp_enqueue_style( 'font' );

				wp_enqueue_script( 'bootstrap' );
				wp_enqueue_style( 'bootstrap' );

				wp_enqueue_style( 'font-awesome' );

				wp_enqueue_style( 'magnific-popup' );
				wp_enqueue_script( 'magnific-popup' );

				wp_enqueue_style( 'ladda' );
				wp_enqueue_script( 'ladda-jquery' );

				wp_enqueue_style( 'select2' );
				wp_enqueue_script( 'select2' );

				wp_enqueue_style( 'jquery-ui' );
				wp_enqueue_script( 'jquery-ui-datepicker' );

				wp_enqueue_style( 'chatjs' );
				wp_enqueue_script( 'chatjs' );

				wp_enqueue_script( 'jquery-validate' );
				wp_enqueue_script( 'hc-sticky' );

				wp_enqueue_script( 'tinymce_js', STS()->asset_url( 'assets/vendors/tinymce/tinymce.min.js' ), false, true );

				$path = admin_url( 'admin-ajax.php' );
				wp_enqueue_style( STS()->assets_handle( 'sts' ) );
				wp_enqueue_script( STS()->assets_handle( 'sts' ) );
				wp_localize_script( STS()->assets_handle( 'sts' ), 'ajaxAdminUrl', array(
					'url' => $path
				) );

				wp_localize_script( STS()->assets_handle( 'sts' ), 'STS_META_DATA', array(
					'is_moderator' => sts_user_is_moderator()
				) );
			}

		}

		public function add_safe_css( $styles ) {
			$styles[] = 'display';

			return $styles;
		}
	}
}