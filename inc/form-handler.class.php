<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'STS_Form_Handler' ) ) {
	class STS_Form_Handler {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {
			add_action( 'wp_loaded', array( $this, 'support_setting_page_save_changed' ) );
			add_action( 'init', array( $this, 'processing_login' ) );
			add_action( 'init', array( $this, 'footer_support_setting' ) );
			add_action( 'init', array( $this, 'update_menu_footer_setting' ) );
			add_action( 'init', array( $this, 'delete_footer_menu' ) );
			add_action( 'init', array( $this, 'save_register_policy' ) );
			add_action( 'wp_logout', array( $this, 'logout_page' ) );
			add_action( 'init', array( $this, 'verify_account' ) );
			add_action( 'init', array( $this, 'lost_password' ) );
			add_action( 'init', array( $this, 'reset_password' ) );
			add_action( 'init', array( $this, 'process_register' ) );
		}

		/**
		 * Save changed support setting
		 */
		public function support_setting_page_save_changed() {
			if ( ! isset( $_POST['sts_support_setting_nonce'] )
			     || ! wp_verify_nonce( $_POST['sts_support_setting_nonce'], 'sts_support_setting_action' )
			) {
				return;
			}
			if ( ! user_can( get_current_user_id(), 'manage_options' ) ) {
				return;
			}
			require_once( ABSPATH . 'wp-admin/includes/template.php' );

			$support_page_id        = sanitize_text_field( $_POST['support_page_id'] );
			$cron_time              = sanitize_text_field( $_POST['cron_time'] );
			$cron_time_close_ticket = sanitize_text_field( $_POST['cron_time_close_ticket'] );
			if ( ! empty( $cron_time ) && ( ! is_numeric( $cron_time ) || $cron_time < 0 ) ) {
				add_settings_error(
					'page_for_support_setting',
					'page_for_support_setting',
					esc_html__( 'Cron time remove lock reply ticket must be a number and positive.', 'sts' ),
					'error'
				);

				return;

			}
			if ( ! empty( $cron_time_close_ticket ) && ( ! is_numeric( $cron_time_close_ticket ) || $cron_time_close_ticket < 0 ) ) {
				add_settings_error(
					'page_for_support_setting',
					'page_for_support_setting',
					esc_html__( 'Cron time close ticket must be a number and positive.', 'sts' ),
					'error'
				);

				return;

			}
			sts_set_support_page_id( absint( $support_page_id ) );
			sts_set_cron_time( absint( $cron_time ) );
			sts_set_cron_time_close_ticket( absint( $cron_time_close_ticket ) );

			add_settings_error(
				'page_for_support_setting',
				'page_for_support_setting',
				esc_html__( 'Support Settings page updated successfully.', 'sts' ),
				'updated'
			);
		}

		public function footer_support_setting() {
			if ( ! isset( $_POST['sts_support_footer_setting_nonce'] )
			     || ! wp_verify_nonce( $_POST['sts_support_footer_setting_nonce'], 'sts_support_footer_setting_action' )
			) {
				return;
			}
			if ( ! user_can( get_current_user_id(), 'manage_options' ) ) {
				return;
			}
			require_once( ABSPATH . 'wp-admin/includes/template.php' );
			$menu_title = sanitize_text_field( $_POST['menu_title'] );
			$menu_link  = sanitize_text_field( $_POST['menu_link'] );
			if ( empty( $menu_title ) || empty( $menu_link ) ) {
				add_settings_error(
					'page_for_support_setting',
					'page_for_support_setting',
					esc_html__( 'Title and link of menu not empty.', 'sts' ),
					'error'
				);

				return;
			}
			if ( filter_var( $menu_link, FILTER_VALIDATE_URL ) === false ) {
				add_settings_error(
					'page_for_support_setting',
					'page_for_support_setting',
					esc_html__( 'Menu link of menu must be an url.', 'sts' ),
					'error'
				);

				return;
			}
			$menus = get_option( 'sts_menu_footer' );
			if ( $menus == false ) {
				$new_menus[] = array( 'key' => 0, 'menu_title' => $menu_title, 'menu_link' => $menu_link );
			} else {
				$new_menus = unserialize( $menus );
				$key       = count( $new_menus );
				array_push( $new_menus, array(
					'key'        => $key,
					'menu_title' => $menu_title,
					'menu_link'  => $menu_link
				) );
			}
			sts_set_menu_footer( serialize( $new_menus ) );


			add_settings_error(
				'page_for_support_setting',
				'page_for_support_setting',
				esc_html__( 'A menu has been save successful!.', 'sts' ),
				'updated'
			);


		}

		public function update_menu_footer_setting() {
			if ( ! isset( $_POST['sts_support_footer_setting__edit_menu_nonce'] )
			     || ! wp_verify_nonce( $_POST['sts_support_footer_setting__edit_menu_nonce'], 'sts_support_footer_setting_edit_menu_action' )
			) {
				return;
			}
			if ( ! user_can( get_current_user_id(), 'manage_options' ) ) {
				return;
			}
			require_once( ABSPATH . 'wp-admin/includes/template.php' );
			$menu_title = sanitize_text_field( $_POST['menu_title'] );
			$menu_link  = sanitize_text_field( $_POST['menu_link'] );
			$key        = sanitize_text_field( $_POST['key'] );
			if ( empty( $menu_title ) || empty( $menu_link ) || empty( $key ) ) {
				add_settings_error(
					'page_for_support_setting',
					'page_for_support_setting',
					esc_html__( 'Title and link of menu not empty.', 'sts' ),
					'error'
				);

				return;
			}
			if ( filter_var( $menu_link, FILTER_VALIDATE_URL ) === false ) {
				add_settings_error(
					'page_for_support_setting',
					'page_for_support_setting',
					esc_html__( 'Menu link of menu must be an url.', 'sts' ),
					'error'
				);

				return;
			}
			$menus = get_option( 'sts_menu_footer' );

			if ( $menus != false ) {
				$new_menus = unserialize( $menus );
				for ( $i = 0; $i < count( $new_menus ); $i ++ ) {
					if ( $new_menus[ $i ]['key'] == $key ) {
						$new_arr         = array_replace( $new_menus[ $i ], array(
							'key'        => $key,
							'menu_title' => $menu_title,
							'menu_link'  => $menu_link
						) );
						$new_menus[ $i ] = $new_arr;
						sts_set_menu_footer( serialize( $new_menus ) );
						break;
					}


				}
				add_settings_error(
					'page_for_support_setting',
					'page_for_support_setting',
					esc_html__( 'A menu has been saved successful!.', 'sts' ),
					'updated'
				);
			} else {
				add_settings_error(
					'page_for_support_setting',
					'page_for_support_setting',
					esc_html__( 'Have not any menu!.', 'sts' ),
					'error'
				);
			}

		}

		public function delete_footer_menu() {
			if ( ! isset( $_GET['nonce'] ) || ! check_ajax_referer( 'sts_delete_footer_menu', 'nonce' ) ) {
				return;
			}
			if ( ! user_can( get_current_user_id(), 'manage_options' ) ) {
				return;
			}
			if ( ! isset( $_GET['key'] ) ) {
				return;
			}
			$key = sanitize_text_field( $_GET['key'] );
			if ( empty( $key ) ) {
				return;
			}
			$menus = get_option( 'sts_menu_footer' );
			require_once( ABSPATH . 'wp-admin/includes/template.php' );
			if ( $menus != false ) {
				$new_menus = unserialize( $menus );
				for ( $i = 0; $i < count( $new_menus ); $i ++ ) {
					if ( $new_menus[ $i ]['key'] == $key ) {
						unset( $new_menus[ $i ] );
						sts_set_menu_footer( serialize( array_values( $new_menus ) ) );
						break;
					}
				}
				wp_redirect( admin_url( '?page=support-setting&tab=footer-settings' ) );
				exit();
			} else {
				add_settings_error(
					'page_for_support_setting',
					'page_for_support_setting',
					esc_html__( 'Have not any menu!.', 'sts' ),
					'error'
				);
			}

		}

		public function save_register_policy() {
			if ( ! isset( $_POST['sts_support_register_setting_nonce'] )
			     || ! wp_verify_nonce( $_POST['sts_support_register_setting_nonce'], 'sts_support_register_setting_action' )
			) {
				return;
			}
			if ( ! user_can( get_current_user_id(), 'manage_options' ) ) {
				return;
			}
			$content = wpautop( stripslashes( wp_filter_post_kses( $_POST['sts-setting-register'] ) ) );
			sts_set_policy_content( $content );
			require_once( ABSPATH . 'wp-admin/includes/template.php' );
			add_settings_error(
				'page_for_support_setting',
				'page_for_support_setting',
				esc_html__( 'Register Settings page updated successfully.', 'sts' ),
				'updated'
			);

		}

		public function process_register() {
			if ( isset( $_POST['sts_register_nonce_field'] ) &&
			     wp_verify_nonce( $_POST['sts_register_nonce_field'], 'processing-register' )
			) {
				$firstName  = sanitize_text_field( $_POST['firstName'] );
				$lastName   = sanitize_text_field( $_POST['lastName'] );
				$email      = sanitize_email( $_POST['email'] );
				$password   = sanitize_text_field( $_POST['password'] );
				$rppassword = sanitize_text_field( $_POST['rppassword'] );
				$result     = apply_filters( 'sts_register', array(
					'firstName'  => $firstName,
					'lastname'   => $lastName,
					'email'      => $email,
					'password'   => $password,
					'rppassword' => $rppassword
				) );

				$error_arr = $result['error'];
				STS()->notice()->set( $error_arr );
			}
		}

		/*
		 * Processing login
		 */
		function processing_login() {
			if ( isset( $_POST['sts_login_nonce_field'] ) && wp_verify_nonce( $_POST['sts_login_nonce_field'], 'processing-login' ) ) {
				do_action( 'sts_before_login_handler' );

				if ( apply_filters( 'sts_cancel_login', false ) ) {
					return;
				}

				$user_name = sanitize_text_field( $_POST['log'] );
				$pass_word = sanitize_text_field( $_POST['pwd'] );
				$remember  = false;
				if ( isset( $_POST['redirect_url'] ) ) {
					$url = esc_url_raw( $_POST['redirect_url'] );
				} else {
					$url = sts_support_page_url();
				}
				if ( isset( $_POST['rememberme'] ) ) {
					'on' == sanitize_text_field( $_POST['rememberme'] ) ? $remember = true : $remember = false;
				}
				$creds = array(
					'user_login'    => $user_name,
					'user_password' => $pass_word,
					'remember'      => $remember
				);

				$user = wp_signon( $creds, false );
				if ( ! is_wp_error( $user ) ) {
					$is_lock = get_user_meta( $user->ID, 'sts_is_lock', true );
					if ( $is_lock == 0 ) {
						$is_first_login = get_user_meta( $user->ID, 'sts_first_login', true );
						if ( $is_first_login == 1 ) {
							update_user_meta( $user->ID, 'sts_first_login', 0 );
						}

						do_action( 'sts_after_login_done' );
						wp_redirect( $url );
						exit;
					} else {
						wp_redirect( sts_support_page_url( 'login/?login=locked' ) );
						do_action( 'sts_after_login_fail' );
						exit;
					}
				} else {
					if ( $user->get_error_code() === 'incorrect_password' ) {
						$message = sprintf( __( ' <strong>ERROR</strong>: the password you entered for the username <strong>%s</strong> is incorrect. <a href="%s">Lost your password?</a>', 'sts' ), esc_html( $user_name ), esc_url( sts_support_page_url( 'lost-password' ) ) );
					} else {
						$message = $user->get_error_message();
					}
					STS()->notice()->set( '<div class="form__message--error">' . wp_kses_post( $message ) . '</div>' );
				}

				do_action( 'sts_after_login_handler' );
			}
		}

		/*
		 * Redirect to logout page
		 */
		function logout_page() {
			$referrer = $_SERVER['HTTP_REFERER'];
			if ( ! empty( $referrer ) && strstr( $referrer, 'wp-login' ) === false && strstr( $referrer, 'wp-admin' ) === false ) {
				$login_page = sts_support_page_url( 'login/' );
				wp_redirect( $login_page . '?login=false' );
				exit;
			}
		}

		public function verify_account() {
			if ( isset( $_GET['key_verify'] ) && $_GET['key_verify'] = 'sts_user_verify_account_security' ) {
				$current_user = wp_get_current_user();
				if ( $current_user->exists() ) {
					update_user_meta( $current_user->ID, 'sts_is_verified', 1 );
				}
			}

		}

		public function lost_password() {
			if ( isset( $_POST['sts_lost_password_nonce_field'] )
			     && wp_verify_nonce( $_POST['sts_lost_password_nonce_field'], 'processing-lost-password' )
			) {
				try {
					$login_user = sanitize_text_field( $_POST['user_login'] );
					if ( empty( $login_user ) ) {
						throw new Exception( __( '<strong>ERROR:</strong> Enter a username or email address.', 'sts' ) );
					}
					if ( ! is_email( $login_user ) ) {
						$user = get_user_by( 'login', $login_user );
						if ( ! $user ) {
							throw new Exception( __( '<strong>ERROR:</strong> This user has not existed!!!', 'sts' ) );
						} else {
							$email = $user->user_email;
						}
					} else {
						$user = get_user_by( 'email', $login_user );
						if ( ! $user ) {
							throw new Exception( __( '<strong>ERROR:</strong> This user has not existed!!!', 'sts' ) );
						} else {
							$email = $user->user_email;
						}
					}
					$user_login = $user->user_login;
					$errors     = new WP_Error();

					do_action( 'lostpassword_post', $errors );
					do_action( 'retrieve_password', $user_login );
					$allow = apply_filters( 'allow_password_reset', true, $user->ID );

					if ( ! $allow ) {
						throw new Exception( __( '<strong>ERROR:</strong> Password reset is not allowed for this user.', 'directory-plus' ) );

					} elseif ( is_wp_error( $allow ) ) {
						throw new Exception( $allow->get_error_message() );
					}

					if ( $errors->get_error_code() ) {
						throw new Exception( $errors->get_error_message() );
					}
					$template = STS()->db()->mail_template()->get_mail_template_by_key( 'mail_reset_password' );
					if ( $template ) {
						$subject = $template->subject;
						$message = sts_replace_content( $template->content,
							array( '%customer_name', '%reset_password_url', '%url_unsubscribe' ),
							array(
								$user->display_name,
								'<a href="' . esc_url( sts_support_page_url( 'reset-password/?email=' . $email . '&key_reset=' . wp_create_nonce( 'sts_lost_password_security' ) ) ) . '" style="color:white;">' . esc_html__( 'Link for reser password', 'sts' ) . '</a>',
								'<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $user->ID . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
							)
						);
					} else {
						$subject = "Mail reset password";
						ob_start();
						STS()->get_template( 'mails/template-mail-reset-password.php',
							array(
								'customer_name'      => $user->display_name,
								'reset_password_url' => '<a href="' . esc_url( sts_support_page_url( 'reset-password/?email=' . $email . '&key_reset=' . wp_create_nonce( 'sts_lost_password_security' ) ) ) . '" style="color:white;">' . esc_html__( 'Link for reser password', 'sts' ) . '</a>',
								'unsubscribe_url'    => '<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $user->ID . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
							) );
						$message = ob_get_clean();
					}

					$result = sts_send_mail( $email, $subject, $message );
					if ( ! $result ) {
						global $phpmailer;
						if ( isset( $phpmailer ) ) {
							throw new Exception( $phpmailer->ErrorInfo );
						}
					}
					STS()->notice()->set( __( '<div class="form__message--success">Please confirm your email to create your new password!!!</div>', 'sts' ) );
				} catch ( Exception $e ) {
					STS()->notice()->set( '<div class="form__message--error">' . wp_kses_post( $e->getMessage() ) . '</div>' );

					return false;
				}


			}
		}

		public function reset_password() {
			if ( isset( $_POST['sts_reset_password_nonce_field'] )
			     && wp_verify_nonce( $_POST['sts_reset_password_nonce_field'], 'processing-reset-password' )
			) {
				try {
					$password   = sanitize_text_field( $_POST['password'] );
					$rppassword = sanitize_text_field( $_POST['rppassword'] );
					$email      = sanitize_email( $_POST['email'] );

					if ( empty( $password ) || empty( $rppassword ) || empty( $email ) ) {
						throw new Exception( __( '<strong>ERROR:</strong> Enter your password and repeat it', 'sts' ) );
					}
					if ( ! is_email( $email ) ) {
						throw new Exception( __( '<strong>ERROR:</strong> Email is not empty!', 'sts' ) );
					}
					if ( $password != $rppassword ) {
						throw new Exception( esc_html__( 'Password and repeat password not same!!!', 'sts' ) );
					}
					$errors = new WP_Error();
					do_action( 'validate_password_reset', $errors, wp_get_current_user() );
					if ( $errors->get_error_code() ) {
						throw new Exception( $errors->get_error_message() );
					}
					$user = get_user_by( 'email', $email );
					if ( ! $user->exists() ) {
						throw new Exception( esc_html__( 'User has not existed!!!', 'sts' ) );
					}
					reset_password( $user, $password );
					STS()->notice()->set( __( '<div class="form__message--success">Reset password successful. Please login to use our service!!</div>', 'sts' ) );
				} catch ( Exception $e ) {
					STS()->notice()->set( '<div class="form__message--error">' . wp_kses_post( $e->getMessage() ) . '</div>' );

					return false;
				}
			}
		}


	}
}


