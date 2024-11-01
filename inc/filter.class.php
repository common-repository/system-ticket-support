<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Filter' ) ) {
	class STS_Filter {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {

			add_filter( 'sts_submit_ticket', array( $this, 'submit_ticket' ) );
			add_filter( 'sts_get_categories', array( $this, 'get_categories' ) );
			add_filter( 'sts_get_filter_ticket', array( $this, 'get_filter_ticket' ) );
			add_filter( 'sts_get_all_ticket', array( $this, 'get_all_ticket' ) );
			add_filter( 'sts_filter_customer', array( $this, 'filter_customer' ) );

			add_filter( 'sts_register', array( $this, 'process_register' ) );


		}

		public function submit_ticket( $args = array() ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$error        = array();
			$date         = date( "Y-m-d H:i:s" );
			$current_user = wp_get_current_user();
			$target       = '.form__message';
			if ( $args['themeId'] == '' || $args['subject'] == '' ) {
				$error_message = esc_html__( 'Please fill in required field!', 'sts' );
				$error[]       = array( 'message' => $error_message, 'target' => $target );

			} else {
				$result_upload = sts_upload_file( $args['files'] );
				$error_message = $result_upload['error'];
				$file_arr      = $result_upload['file_arr'];
				if ( $error_message != '' ) {
					$target  = '#form-control-message-file';
					$error[] = array( 'message' => $error_message, 'target' => $target );
				}
				if ( count( $error ) == 0 ) {
					if ( ! $current_user->exists() ) {
						$result = sts_processing_register( $args['firstName'], $args['lastName'], $args['email'], $args['password'], $args['rpassword'] );
						if ( count( $result['error'] ) > 0 ) {
							foreach ( $result['error'] as $item ) {
								$error[] = $item;
							}
						}
						$is_new = true;
					} else {
						sts_logout_locked( $current_user->ID );
						$is_new = false;
					}
					if ( count( $error ) == 0 ) {
						$current_user = wp_get_current_user();
						$user_id      = $current_user->ID;
						$table_name   = $table_prefix . 'ticket';
						$result       = $wpdb->insert(
							$table_name,
							array(
								'subject'              => $args['subject'],
								'theme_id'             => $args['themeId'],
								'created_date'         => $date,
								'customer_id'          => $user_id,
								'status'               => 1,
								'updating_date'        => $date,
								'latest_updating_date' => $date,
							)
						);
						if ( $result === false || $result === 0 ) {
							$error_message = esc_html__( 'Can not save ticket!!', 'sts' );
							$error[]       = array( 'message' => $error_message, 'target' => $target );
						} else {
							$ticket_id  = $wpdb->insert_id;
							$table_name = $table_prefix . 'message';
							$wpdb->insert(
								$table_name,
								array(
									'message'      => $args['ticketMessage'],
									'created_date' => $date,
									'user_id'      => $user_id,
									'ticket_id'    => $ticket_id,
									'is_question'  => true
								)
							);
							$messageID = $wpdb->insert_id;
							if ( ! empty( array_filter( $file_arr ) ) ) {
								$table_name = $table_prefix . 'attachment';
								foreach ( $file_arr as $f ) {
									$wpdb->insert(
										$table_name,
										array(
											'attachment_name' => $f['name'],
											'attachment_url'  => $f['url'],
											'message_id'      => $messageID,
										)
									);
								}
							}
						}
					}
				}
			}

			return array( 'errors' => $error, 'is_new' => $is_new );
		}

		public function get_categories( $ticket ) {

			$theme = STS()->db()->themes()->getting_theme_by_id( $ticket->theme_id );

			return $theme;
		}

		public function get_filter_ticket( $args = array() ) {
			$current_user = wp_get_current_user();
			$user_meta    = get_userdata( $current_user->ID );
			$user_roles   = $user_meta->roles;
			if ( in_array( 'subscriber', $user_roles ) ) {
				$ticket_arr = STS()->db()->tickets()->get_tickets_by_customer_filter( $args['status'], $args['keyword'], $args['ftstatus'], $args['user_id'], $args['theme'], $args['offset'] );
			} else {
				$ticket_arr = STS()->db()->tickets()->get_all_tickets_filter( $args['status'], $args['keyword'], $args['ftstatus'], $args['theme'], $args['offset'], $args['user_id'] );
			}

			return $ticket_arr;
		}

		public function get_all_ticket( $args = array() ) {
			$current_user = wp_get_current_user();
			$user_meta    = get_userdata( $current_user->ID );
			$user_roles   = $user_meta->roles;
			if ( in_array( 'subscriber', $user_roles ) ) {
				$ticket_arr = STS()->db()->tickets()->get_tickets_by_customer( $args['status'], $args['user_id'], $args['offset'], $args['theme'] );
			} else {
				$ticket_arr = STS()->db()->tickets()->get_all_tickets( $args['status'], $args['offset'], $args['user_id'], $args['theme'] );
			}

			return $ticket_arr;
		}

		public function filter_customer( $args = array() ) {

			$all_customers = get_users( array(
				'role'   => 'subscriber',
				'search' => $args['keyword'],
			) );
			$customers     = get_users( array(
				'role'   => 'subscriber',
				'offset' => $args['offset'],
				'number' => $args['limit'],
				'search' => $args['keyword']
			) );


			$numbPage  = ceil( count( $all_customers ) / $args['limit'] );
			$paginator = '';
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
				STS()->get_template( 'customers/customer-item.php',
					array( 'customers' => $contents, 'nonce' => wp_create_nonce( 'sts_frontend_delete_customer' ) ) );
				$content = ob_get_clean();
				if ( $numbPage > 1 ) {
					ob_start();
					STS()->get_template( 'paginator.php',
						array(
							'numbPage'     => $numbPage,
							'current_page' => $args['current_page'],
							'target'       => '#form-paginator-listing-customer',
							'limit'        => $args['limit'],
							'total'        => count( $all_customers )
						) );
					$paginator = ob_get_clean();
				}
				$arr_param = array(
					'target'            => 'table.customer-table>tbody',
					'content'           => $content,
					'current_page'      => intval( $args['current_page'] ),
					'total_page'        => $numbPage,
					'target_paginator'  => '.listing-customer-paginator',
					'content_paginator' => $paginator
				);
			} else {
				$arr_param = array(
					'target'            => 'table.customer-table>tbody',
					'content'           => '<td colspan="100%">' . esc_html__( 'No customer found!!', 'sts' ) . '</td>',
					'target_paginator'  => '.listing-customer-paginator',
					'content_paginator' => $paginator
				);

			}

			return $arr_param;
		}


		public function process_register( $args = array() ) {
			return sts_processing_register( $args['firstName'], $args['lastname'], $args['email'], $args['password'], $args['rppassword'] );
		}

	}
}