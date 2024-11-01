<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Endpoints' ) ) {
	class STS_Endpoints {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {
			add_action( 'init', array( $this, 'add_endpoints' ) );
			add_action( 'template_redirect', array( $this, 'redirect_to_dashboards' ) );


			// Add endpoints template
			add_action( 'tst_endpoint_template_submit-ticket', array( $this, 'submit_ticket_template' ) );
			add_action( 'tst_endpoint_template_no_login_submit-ticket', array( $this, 'submit_ticket_template' ) );
			add_action( 'tst_endpoint_template_tickets', array( $this, 'tickets_template' ) );
			add_action( 'tst_endpoint_template_ticket-details', array( $this, 'ticket_details_template' ) );
			add_action( 'tst_endpoint_template_customers', array( $this, 'customers_template' ) );
			add_action( 'tst_endpoint_template_customer-details', array( $this, 'customer_details_template' ) );
			add_action( 'tst_endpoint_template_editing-profile', array( $this, 'editing_customer_template' ) );
			add_action( 'tst_endpoint_template_supporters', array( $this, 'supporters_template' ) );
			add_action( 'tst_endpoint_template_supporter-details', array( $this, 'supporter_details_template' ) );
			add_action( 'tst_endpoint_template_templates', array( $this, 'templates_template' ) );
			add_action( 'tst_endpoint_template_new-template', array( $this, 'new_template_template' ) );
			add_action( 'tst_endpoint_template_update-template', array( $this, 'update_template_template' ) );
			add_action( 'tst_endpoint_template_categories', array( $this, 'categories_template' ) );
			add_action( 'tst_endpoint_template_updating-category', array( $this, 'updating_category_template' ) );
			add_action( 'tst_endpoint_template_new-category', array( $this, 'new_category_template' ) );
			add_action( 'tst_endpoint_template_dashboards', array( $this, 'dashboards_template' ) );
			add_action( 'tst_endpoint_template_user-profile', array( $this, 'your_profile_template' ) );
			add_action( 'tst_endpoint_template_no_login_login', array( $this, 'login_template' ) );
			add_action( 'tst_endpoint_template_no_login_register', array( $this, 'register_template' ) );
			add_action( 'tst_endpoint_template_rating-report', array( $this, 'rating_report_template' ) );
			add_action( 'tst_endpoint_template_no_login_lost-password', array( $this, 'lost_password_template' ) );
			add_action( 'tst_endpoint_template_no_login_reset-password', array( $this, 'reset_password_template' ) );
			add_action( 'tst_endpoint_template_new-mail-template', array( $this, 'new_mail_template_template' ) );
			add_action( 'tst_endpoint_template_mail-templates', array( $this, 'mail_templates_template' ) );
			add_action( 'tst_endpoint_template_update-email-template', array(
				$this,
				'update_email_template_template'
			) );
			add_action( 'tst_endpoint_template_supporter-report', array( $this, 'supporter_report_template' ) );
		}

		/**
		 * Endpoints list for support page
		 *
		 * @return array
		 */
		public function endpoint_list() {
			$endpoints = array(
				'submit-ticket'         => esc_html__( 'Submit Ticket', 'sts' ),
				'register'              => esc_html__( 'Register', 'sts' ),
				'ticket-details'        => esc_html__( 'Ticket Details', 'sts' ),
				'tickets'               => esc_html__( 'Listing tickets', 'sts' ),
				'login'                 => esc_html__( 'Login', 'sts' ),
				'customers'             => esc_html__( 'Customers', 'sts' ),
				'supporters'            => esc_html__( 'Supporters', 'sts' ),
				'supporter-details'     => esc_html__( 'Supporter details', 'sts' ),
				'customer-details'      => esc_html__( 'Customer details', 'sts' ),
				'new-template'          => esc_html__( 'New template', 'sts' ),
				'templates'             => esc_html__( 'Listing templates', 'sts' ),
				'update-template'       => esc_html__( 'Update template', 'sts' ),
				'new-category'          => esc_html__( 'New category', 'sts' ),
				'categories'            => esc_html__( 'Listing categories', 'sts' ),
				'dashboards'            => esc_html__( 'Dashboards', 'sts' ),
				'rating-report'         => esc_html__( 'Report', 'sts' ),
				'updating-category'     => esc_html__( 'Updating category', 'sts' ),
				'lost-password'         => esc_html__( 'Lost password', 'sts' ),
				'reset-password'        => esc_html__( 'Reset password', 'sts' ),
				'user-profile'          => esc_html__( 'User Profile', 'sts' ),
				'update-email-template' => esc_html__( 'Update subject mail', 'sts' ),
				'mail-templates'        => esc_html__( 'Listing mail templates', 'sts' ),
				'supporter-report'      => esc_html__( 'Report', 'sts' ),
			);

			return $endpoints;
		}

		public function redirect_to_dashboards() {
			$current_user = wp_get_current_user();
			$is_lock      = get_user_meta( $current_user->ID, 'sts_is_lock', true );
			if ( $current_user->exists() && $this->is_current_endpoint( 'login' ) && $is_lock != 1 ) {
				wp_redirect( sts_support_page_url( 'dashboards' ) );
				exit();
			}
			$is_can_register = get_option( 'users_can_register' );
			if ( $this->is_current_endpoint( 'register' ) && $is_can_register != 1 ) {
				wp_redirect( sts_support_page_url( 'login' ) );
				exit();
			}
			if (
				$this->is_current_endpoint( 'mail-templates' )
				|| $this->is_current_endpoint( 'new-category' )
				|| $this->is_current_endpoint( 'rating-report' )
				|| $this->is_current_endpoint( 'supporter-report' )
				|| $this->is_current_endpoint( 'supporters' )
				|| $this->is_current_endpoint( 'categories' ) ) {
				sts_redirect_not_login();
				if ( ! user_can( $current_user, 'administrator' ) ) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				}
			}
			if ( $this->is_current_endpoint( 'customer-details' ) ) {
				sts_redirect_not_login();
				if ( ! isset( $_GET['customer_id'] ) || $_GET['customer_id'] == '' ||
				     ( ! user_can( $current_user, 'administrator' )
				       && ! user_can( $current_user, 'leader_supporter' )
				       && ! user_can( $current_user, 'supporter' )
				       && ! user_can( $current_user, 'subscriber' ) )
				     || ( ( user_can( $current_user, 'subscriber' ) && $current_user->ID != $_GET['customer_id'] ) )
				) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				} else {
					$customer_id = sanitize_text_field( $_GET['customer_id'] );
					$customer    = get_user_by( 'ID', $customer_id );
					if ( ! $customer || ! user_can( $customer, 'subscriber' ) ) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
				}

			}
			if ( $this->is_current_endpoint( 'ticket-details' ) ) {
				sts_redirect_not_login();
				if ( ! isset( $_GET['t'] ) || $_GET['t'] == '' ||
				     ( ! user_can( $current_user, 'administrator' )
				       && ! user_can( $current_user, 'leader_supporter' )
				       && ! user_can( $current_user, 'supporter' )
				       && ! user_can( $current_user, 'subscriber' ) )
				) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				} else {
					$ticket_id = sanitize_text_field( $_GET['t'] );
					$ticket    = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
					if ( ! $ticket || ( user_can( $current_user, 'subscriber' ) && $current_user->ID != $ticket->customer_id ) ) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
				}

			}
			if ( $this->is_current_endpoint( 'customers' ) ||
			     $this->is_current_endpoint( 'new-template' )
			     || $this->is_current_endpoint( 'templates' ) ) {
				sts_redirect_not_login();
				if ( ! user_can( $current_user, 'administrator' )
				     && ! user_can( $current_user, 'leader_supporter' )
				     && ! user_can( $current_user, 'supporter' ) ) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				}

			}
			if (
			$this->is_current_endpoint( 'tickets' ) ) {
				sts_redirect_not_login();
				if ( ! user_can( $current_user, 'administrator' )
				     && ! user_can( $current_user, 'leader_supporter' )
				     && ! user_can( $current_user, 'supporter' )
				     && ! user_can( $current_user, 'subscriber' ) ) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				}

			}
			if ( $this->is_current_endpoint( 'supporter-details' ) ) {
				sts_redirect_not_login();
				if ( ( ! user_can( $current_user, 'administrator' )
				       && ! user_can( $current_user, 'leader_supporter' )
				       && ! user_can( $current_user, 'supporter' )
				       && ! user_can( $current_user, 'subscriber' ) ) ||
				     ! isset( $_GET['supporter_id'] ) || $_GET['supporter_id'] == '' ) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				} else {
					$supporter_id = sanitize_text_field( $_GET['supporter_id'] );
					$supporter    = get_user_by( 'ID', $supporter_id );
					if ( ! $supporter || ( ! user_can( $supporter, 'leader_supporter' )
					                       && ! user_can( $supporter, 'supporter' ) &&
					                       ! user_can( $supporter, 'administrator' ) ) ) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
				}

			}
			if ( $this->is_current_endpoint( 'update-email-template' ) ) {
				sts_redirect_not_login();
				if ( ! user_can( $current_user, 'administrator' ) || ! isset( $_GET['id'] ) || $_GET['id'] == '' ) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				} else {
					$id           = sanitize_text_field( $_GET['id'] );
					$mail_subject = STS()->db()->mail_template()->get_mail_template_by_id( $id );
					if ( ! $mail_subject ) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
				}

			}
			if ( $this->is_current_endpoint( 'update-template' ) ) {
				sts_redirect_not_login();
				if ( ! isset( $_GET['template_id'] ) || $_GET['template_id'] == '' ) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				} else {
					$id       = sanitize_text_field( $_GET['template_id'] );
					$template = STS()->db()->templates()->getting_template_by_id( $id );
					if ( ! $template ||
					     ( ! user_can( $current_user, 'administrator' )
					       && ! user_can( $current_user, 'leader_supporter' )
					       && ! user_can( $current_user, 'supporter' ) )
					     || ( user_can( $current_user, 'supporter' ) &&
					          ( $template->user_id != $current_user->ID || $template->is_public == 1 ) )
					) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
				}

			}
			if ( $this->is_current_endpoint( 'updating-category' ) ) {
				sts_redirect_not_login();
				if ( ! user_can( $current_user, 'administrator' ) || ! isset( $_GET['cat_id'] ) || $_GET['cat_id'] == '' ) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				} else {
					$id    = sanitize_text_field( $_GET['cat_id'] );
					$theme = STS()->db()->themes()->getting_theme_by_theme_id( $id );
					if ( ! $theme ) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
				}

			}
			if ( $this->is_current_endpoint( 'user-profile' ) ) {
				sts_redirect_not_login();
				if ( ( ! isset( $_GET['profile_id'] ) || $_GET['profile_id'] == '' ) &&
				     ( ! isset( $_GET['page'] ) || $_GET['page'] != 'my-profile' ) ) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				} else {
					if ( ( ! user_can( $current_user, 'administrator' )
					       && ! user_can( $current_user, 'leader_supporter' )
					       && ! user_can( $current_user, 'supporter' )
					       && ! user_can( $current_user, 'subscriber' ) ) ) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
					if ( isset( $_GET['profile_id'] ) && $_GET['profile_id'] != '' ) {
						$user_id = sanitize_text_field( $_GET['profile_id'] );
					} elseif ( isset( $_GET['page'] ) && $_GET['page'] == 'my-profile' ) {
						$user_id = $current_user->ID;
					}
					$user = get_user_by( 'ID', $user_id );
					if ( ! $user ) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
					$user_meta  = get_userdata( $user_id );
					$user_roles = $user_meta->roles;
					if ( ( $current_user->ID != $user_id && user_can( $current_user, 'subscriber' ) )
					) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
					if ( user_can( $current_user, 'leader_supporter' ) && ( $current_user->ID != $user_id && ! in_array( 'subscriber', $user_roles ) ) ) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}
					if ( user_can( $current_user, 'supporter' ) && ( $current_user->ID != $user_id && ! in_array( 'subscriber', $user_roles ) ) ) {
						wp_redirect( sts_support_page_url( 'dashboards' ) );
						exit();
					}

				}
			}
			if ( $this->is_current_endpoint( 'lost-password' ) ||
			     $this->is_current_endpoint( 'reset-password' ) ) {
				if ( $current_user->exists() ) {
					wp_redirect( sts_support_page_url( 'dashboards' ) );
					exit();
				}
			}
		}

		public function get_endpoints_mask() {
			if ( 'page' === get_option( 'show_on_front' ) ) {
				$page_on_front   = intval( get_option( 'page_on_front', '-1' ) );
				$support_page_id = sts_support_page_id();
				if ( $page_on_front === $support_page_id ) {
					return EP_ROOT | EP_PAGES;
				}
			}

			return EP_PAGES;
		}

		/**
		 * Add endpoint for page
		 */
		public function add_endpoints() {
			$mask = $this->get_endpoints_mask();
			foreach ( $this->endpoint_list() as $endpoint_key => $endpoint_key_value ) {
				add_rewrite_endpoint( $endpoint_key, $mask );
			}
		}


		/**
		 * Get current endpoint
		 *
		 * @return mixed
		 */
		public function get_current_endpoint() {
			global $wp_query;

			foreach ( $this->endpoint_list() as $endpoint_key => $endpoint_key_value ) {
				if ( isset( $wp_query->query_vars[ $endpoint_key ] ) ) {
					return $endpoint_key;
				}
			}

			return '';
		}

		/**
		 * Check endpoint is current
		 *
		 * @param $endpoint
		 *
		 * @return bool
		 */
		public function is_current_endpoint( $endpoint ) {
			global $wp_query;

			return isset( $wp_query->query_vars[ $endpoint ] );
		}

		/*--------------------------------------------------------------
		/* Endpoint template method
		--------------------------------------------------------------*/

		/**
		 * Submit ticket template
		 */
		public function submit_ticket_template() {
			STS()->get_template( 'endpoints/submit-ticket.php' );
		}

		/**
		 * Tickets template
		 */
		public function tickets_template() {
			STS()->get_template( 'endpoints/tickets.php' );
		}

		/**
		 * Ticket details template
		 */
		public function ticket_details_template() {
			STS()->get_template( 'endpoints/ticket-details.php' );
		}

		/**
		 * Customer template
		 */
		public function customers_template() {
			STS()->get_template( 'endpoints/customers.php' );
		}

		/**
		 * Customer details template
		 */
		public function customer_details_template() {
			STS()->get_template( 'endpoints/customer-details.php' );
		}

		/**
		 * Supporter templates
		 */
		public function supporters_template() {
			STS()->get_template( 'endpoints/supporters.php' );
		}

		/**
		 * Supporter details template
		 */
		public function supporter_details_template() {
			STS()->get_template( 'endpoints/supporter-details.php' );
		}

		/**
		 * Update template message
		 */
		public function templates_template() {
			STS()->get_template( 'endpoints/templates.php' );
		}

		/**
		 * New template message
		 */
		public function new_template_template() {
			STS()->get_template( 'endpoints/new-template.php' );
		}

		/**
		 * Update template
		 */
		public function update_template_template() {
			STS()->get_template( 'endpoints/update-template.php' );
		}

		/*
		 * Theme template
		 */
		public function categories_template() {
			STS()->get_template( 'endpoints/categories.php' );
		}

		/**
		 * Update theme template
		 */
		public function updating_category_template() {
			STS()->get_template( 'endpoints/updating-category.php' );
		}

		/**
		 * New theme template
		 */
		public function new_category_template() {
			STS()->get_template( 'endpoints/new-category.php' );
		}

		/**
		 * Dashboards template
		 */
		public function dashboards_template() {
			STS()->get_template( 'endpoints/dashboards.php' );
		}

		/**
		 * Your profile template
		 */
		public function your_profile_template() {
			STS()->get_template( 'endpoints/user-profile.php' );
		}

		public function rating_report_template() {
			STS()->get_template( 'endpoints/rating-report.php' );
		}

		/**
		 * Lost password template
		 */
		public function lost_password_template() {
			STS()->get_template( 'endpoints/lost-password.php' );
		}

		/**
		 * Reset password template
		 */
		public function reset_password_template() {
			STS()->get_template( 'endpoints/reset-password.php' );
		}


		/**
		 * Register template
		 */
		public function register_template() {
			STS()->get_template( 'endpoints/register.php' );
		}

		/**
		 * Login template
		 */
		public function login_template() {

			STS()->get_template( 'endpoints/login.php',
				sts_set_content_form_login() );

		}

		/**
		 * New subject mail template
		 */
		public function new_mail_template_template() {
			STS()->get_template( 'endpoints/new-mail-template.php' );
		}

		/**
		 * Mail subjects template
		 */
		public function mail_templates_template() {
			STS()->get_template( 'endpoints/mail-templates.php' );
		}

		/**
		 * Update mail subject template
		 */
		public function update_email_template_template() {
			STS()->get_template( 'endpoints/update-mail-template.php' );
		}

		/**
		 * Supporter report
		 */
		public function supporter_report_template() {
			STS()->get_template( 'endpoints/supporter-report.php' );
		}

	}
}