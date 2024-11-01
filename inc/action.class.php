<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Action' ) ) {
	class STS_Action {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {
			add_action( 'sts_submit_ticket_before', array( $this, 'create_form_control_category' ) );
			add_action( 'sts_customer_top', array( $this, 'create_input_search_customer' ) );
			add_action( 'sts_menu_left_middle', array( $this, 'set_menu_category' ) );
			add_action( 'sts_tickets_filter_second', array( $this, 'set_label_filter_category' ) );
			add_action( 'sts_customer_item', array( $this, 'customer_items' ) );
		}

		public function create_form_control_category() {
			$themes       = STS()->db()->themes()->getting_theme_active();
			$current_user = wp_get_current_user();
			ob_start();
			STS()->get_template( 'themes/form-control-theme.php',
				array(
					'themes'       => $themes,
					'current_user' => $current_user->exists()
				) );
			$content = ob_get_clean();

			echo $content;

		}

		public function create_input_search_customer() {
			ob_start();
			STS()->get_template( 'customers/input-search.php',
				array() );
			$content = ob_get_clean();

			echo $content;
		}

		public function set_menu_category() {
			ob_start();
			STS()->get_template( 'menu-category.php',
				array(
					'is_endpoint_category' => STS()->endpoints()->is_current_endpoint( 'categories' ),
				) );
			$content = ob_get_clean();

			echo $content;
		}

		public function set_label_filter_category() {
			ob_start();
			STS()->get_template( 'themes/label_filter_theme.php',
				array() );
			$content = ob_get_clean();

			echo $content;
		}

		public function customer_items() {
			$limit     = STS()->db()->limit_user;
			$customers = get_users( array( 'role' => 'subscriber', 'offset' => 0, 'number' => $limit ) );
			if ( $customers ) {
				$contents = array();
				foreach ( $customers as $customer ) {
					$ticketNumb = count( STS()->db()->tickets()->get_ticket_by_customer_all( $customer->ID ) );
					$is_lock    = get_user_meta( $customer->ID, 'sts_is_lock', true );
					$username   = $customer->user_login;
					$contents[] = array(
						'id'         => $customer->ID,
						'username'   => $username,
						'name'       => $customer->display_name,
						'email'      => $customer->user_email,
						'registered' => $customer->user_registered,
						'ticketNumb' => $ticketNumb,
						'is_lock'    => $is_lock,
						'avatar'     => sts_get_avatar( $customer->ID, 100, 100 )
					);
				}
				ob_start();
				STS()->get_template( 'customers/customer-item.php', array(
					'customers' => $contents,
				) );
				$content = ob_get_clean();
			} else {
				$content = '<tr><td colspan="100%">' . esc_html__( "No customer found!!", "sts" ) . '</td></tr>';
			}

			echo $content;
		}
	}
}