<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Install' ) ) {
	class STS_Install {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {
			add_action( 'init', array( $this, 'check_update' ) );
		}

		public function check_update() {
			if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'sts_version' ) !== STS()->plugin_ver() ) ) {
				$this->install();
				do_action( 'sts_updated' );
			}
		}

		public function install() {
			if ( ! is_blog_installed() ) {
				return;
			}

			if ( 'yes' === get_transient( 'sts_installing' ) ) {
				return;
			}

			set_transient( 'sts_installing', 'yes', MINUTE_IN_SECONDS * 10 );
			if ( ! defined( 'STS_INSTALLING' ) ) {
				define( 'STS_INSTALLING', true );
			}

			$this->create_tables();
			$this->create_roles();
			$this->create_data_mail_template();
			$this->update_plugin_version();
			$this->create_default_page();

			delete_transient( 'sts_installing' );
			flush_rewrite_rules();

			do_action( 'sts_installed' );
		}

		/**
		 * Create table for plugin
		 */
		public function create_tables() {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			global $wpdb;
			$table_prefix = "{$wpdb->prefix}sts_";

			$charset_collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				$charset_collate = $wpdb->get_charset_collate();
			}

			$table_name = $table_prefix . 'ticket';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        subject text NOT NULL,
                        created_date datetime NOT NULL,
                        customer_id mediumint(9) NOT NULL,
                        status tinyint(1) NOT NULL,
                        theme_id text NOT NULL,
                        supporter_id mediumint(9) NULL,
                        history longtext NULL,
                        rate_date datetime NULL,
                        rate_info longtext NULL,
                        rate tinyint(1) NULL,
                        assigned_date datetime NULL,
                        close_date datetime NULL,
                        updating_date datetime NULL,
                        latest_updating_date datetime NULL,
                        is_lock tinyint(1) NULL,
                        user_lock mediumint(9) NULL,
                        lock_date datetime NULL,
                        is_rating_visited tinyint(1) NULL,
                        visited_date datetime NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'message';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        message longtext NOT NULL,
                        created_date datetime NOT NULL,
                        user_id mediumint(9) NOT NULL,
                        ticket_id mediumint(9) NOT NULL,
                        is_question tinyint(1) NOT NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'attachment';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        attachment_name varchar(200) NOT NULL,
                        attachment_url varchar(200) NOT NULL,
                        message_id mediumint(9) NOT NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'note';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        message longtext NOT NULL,
                        created_date datetime NOT NULL,
                        ticket_id mediumint(9) NOT NULL,
                        user_id mediumint(9) NOT NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'theme';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        theme_name varchar(200) NULL,
                        theme_id text NULL,
                        status tinyint(1) DEFAULT 1 NOT NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'notification';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        content text NOT NULL,
                        link varchar(200) NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'user_notification';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        notification_id mediumint(9) NOT NULL,
                        user_id mediumint(9) NOT NULL,
                        created_date datetime NOT NULL,
                        is_read tinyint(1) NOT NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'template';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        template_name varchar(200) NOT NULL,
                        template_value text NOT NULL,
                        user_id mediumint(9) NULL,
                        is_public tinyint(1) DEFAULT 0,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'template_tags';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        template_id mediumint(9) NULL,
                        tag varchar(50) NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'user_attachment';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        user_id mediumint(9) NOT NULL,
                        attachment_url text NOT NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );

			$table_name = $table_prefix . 'user_follow_ticket';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        user_id mediumint(9) NOT NULL,
                        ticket_id mediumint(9) NOT NULL,
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );
			$table_name = $table_prefix . 'key_mail_rate';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        key_name varchar(50) NULL,
                        key_code varchar(200) NULL, 
                        user_id mediumint(9) NULL,
                        ticket_id mediumint(9) NULL,
                        date_expired datetime NULL, 
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );
			$table_name = $table_prefix . 'mail_template';
			$sql        = "CREATE TABLE {$table_name} (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        key_name varchar(50) NULL,
                        subject text NULL,
                        content text NULL,
                        created_date datetime NULL, 
                        PRIMARY KEY  (id)
                        ) {$charset_collate};
                        ";
			dbDelta( $sql );
		}

		/**
		 * Create roles for plugin
		 */
		public function create_roles() {
			global $wp_roles;

			if ( ! class_exists( 'WP_Roles' ) ) {
				return;
			}

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}

			// Customer role
			add_role( 'supporter', esc_html__( 'Supporter', 'sts' ), array(
				'read'       => true,
				'edit_posts' => true
			) );
			add_role( 'leader_supporter', esc_html__( 'Leader supporter', 'sts' ), array(
				'read'       => true,
				'edit_posts' => true
			) );
		}

		/**
		 * Update plugin version
		 */
		public function update_plugin_version() {
			delete_option( 'sts_version' );
			add_option( 'sts_version', STS()->plugin_ver() );
		}

		public function create_data_mail_template() {
			global $wpdb;
			$date         = date( "Y-m-d H:i:s" );
			$table_prefix = STS()->db()->table_prefix();
			$table_name   = $table_prefix . 'mail_template';
			$data         = array();
			ob_start();
			STS()->get_template( 'mails/template-mail-reply.php',
				array(
					'customer_name'   => '%customer_name',
					'user_name'       => '%user_reply_name',
					'ticket_url'      => '%ticket_url',
					'unsubscribe_url' => '%url_unsubscribe'
				) );
			$content = ob_get_clean();
			$data[]  = array( 'key'     => 'mail_reply',
			                  'subject' => esc_html__( "Mail reply ticket", 'sts' ),
			                  'content' => $content
			);
			ob_start();
			STS()->get_template( 'mails/template-mail-reset-password.php',
				array(
					'customer_name'      => '%customer_name',
					'reset_password_url' => '%reset_password_url',
					'unsubscribe_url'    => '%url_unsubscribe'
				) );
			$content = ob_get_clean();
			$data[]  = array(
				'key'     => 'mail_reset_password',
				'subject' => esc_html__( "Mail reset password", "sts" ),
				'content' => $content
			);
			ob_start();
			STS()->get_template( 'mails/template-mail-rating.php',
				array(
					'customer_name'   => '%customer_name',
					'ticket_url'      => '%ticket_url',
					'user_name'       => '%user_close_ticket',
					'rating_url'      => '%rating_url',
					'unsubscribe_url' => '%url_unsubscribe'
				) );
			$content = ob_get_clean();
			$data[]  = array( 'key'     => 'mail_close_ticket',
			                  'subject' => esc_html__( "Mail close ticket", "sts" ),
			                  'content' => $content
			);
			ob_start();
			STS()->get_template( 'mails/template-mail-register.php',
				array(
					'customer_name'   => '%customer_name',
					'profile_url'     => '%profile_url',
					'unsubscribe_url' => '%url_unsubscribe'
				) );
			$content = ob_get_clean();
			$data[]  = array( 'key'     => 'mail_register',
			                  'subject' => esc_html__( "Mail register", "sts" ),
			                  'content' => $content
			);
			foreach ( $data as $item ) {
				$mail_template = STS()->db()->mail_template()->get_subject_mail_by_key( $item['key'] );
				if ( ! $mail_template ) {
					$wpdb->insert( $table_name,
						array(
							'key_name'     => $item['key'],
							'subject'      => $item['subject'],
							'content'      => $item['content'],
							'created_date' => $date
						) );
				}
			}
		}

		public function create_default_page() {
			$support_page_id = sts_support_page_id();
			if ( $support_page_id == 0 || $support_page_id == '' ) {
				$post_details = array(
					'post_title'   => 'Support',
					'post_content' => '',
					'post_status'  => 'publish',
					'post_author'  => get_current_user_id(),
					'post_type'    => 'page'
				);
				$page_id      = wp_insert_post( $post_details );
				if ( $page_id !== false ) {
					sts_set_support_page_id( $page_id );
				}
			}
		}
	}
}