<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Ajax' ) ) {
	class STS_Ajax {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		private $_ajax_action = array(
			'load_data_ticket_init',
			'mark_process',
			'unmark_process',
			'change_status',
			'change_supporter',
			'customer_details_paginator',
			'process_customer_filter',
			'delete_note',
			'process_edit_message',
			'mark_follow',
			'process_notification',
			'save_note',
			'unfollow_process',
			'supporter_details_filter',
			'process_submit_ticket',
			'update_customer_profile',
			'update_user_profile',
			'new_category',
			'update_category',
			'new_template',
			'update_template',
			'reply_ticket',
			'save_rating',
			'delete_template',
			'delete_customer',
			'delete_purchase',
			'delete_supporter',
			'delete_ticket',
			'delete_category',
			'delete_message',
			'delete_template_test',
			'template_add_new',
			'category_listing_paginator',
			'get_message',
			'auto_sending',
			'process_get_form_reply',
			'process_cancel_form_reply',
			'ticket_details_auto_sending',
			'update_mail_subject',
			'lock_category',
			'unlock_category',
			'report_filter',
			'unlock_user',
			'get_template',
			'template_listing_paginator',
			'lock_template',
			'unlock_template',
			'supporter_report_filter',
			'get_notification',
			'note_listing_paginator',
			'dashboards_filter_ticket',
			'supporter_report_show_message'
		);

		public function init() {
			foreach ( $this->_ajax_action as $action ) {
				add_action( 'wp_ajax_sts_' . $action, array( $this, $action ) );
				if ( $action == 'process_submit_ticket'
				) {
					add_action( 'wp_ajax_nopriv_sts_' . $action, array( $this, $action ) );
				}
			}
		}

		/*
		 * Filter ticket at page tickets
		 */
		public function load_data_ticket_init() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/process-filter-tickets-all-ajax-action.php' ) );
		}

		/*
		 * Mark process at page ticket details
		 */
		public function mark_process() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/mark-process-ajax-action.php' ) );
		}

		/*
		 * Unmark process at page ticket details
		 */
		public function unmark_process() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/unmark-process-ajax-action.php' ) );
		}

		/*
		 * Change status
		 */
		public function change_status() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/change-status-ajax-action.php' ) );
		}

		/*
		 * Change supporter
		 */
		public function change_supporter() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/change-supporter-ajax-action.php' ) );
		}

		/*
		 * Paginator for ticket of customer
		 */
		public function customer_details_paginator() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/customer-details-paginator-ajax-action.php' ) );
		}

		/*
		 * Filter ticket of customer
		 */
		public function process_customer_filter() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/customer-filter-ajax-action.php' ) );
		}

		/*
		 * Delete note
		 */
		public function delete_note() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/delete-note-ajax-action.php' ) );
		}

		/*
		 * Edit message
		 */
		public function process_edit_message() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/edit-message-ajax-action.php' ) );
		}


		/*
		 * Mark follow
		 */
		public function mark_follow() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/mark-follow-ajax-action.php' ) );
		}

		/*
		 * Process read notification
		 */
		public function process_notification() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/process-notification-ajax-action.php' ) );
		}


		/*
		 * Save note
		 */
		public function save_note() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/save-note-ajax-action.php' ) );
		}

		/*
		 * Unmark follow
		 */
		public function unfollow_process() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/unmark-follow-ajax-action.php' ) );
		}

		/*
		 * Supporter filter
		 */
		public function supporter_details_filter() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/supporter-details-filter-ajax-action.php' ) );
		}

		/**
		 * Submit ticket
		 */
		public function process_submit_ticket() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/submit-ticket-ajax-action.php' ) );
		}

		/**
		 * Update customer profile
		 */
		public function update_customer_profile() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/update-cutomer-profile-ajax-action.php' ) );
		}

		/**
		 * Update user profile
		 */
		public function update_user_profile() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/update-profile-ajax-action.php' ) );
		}

		/**
		 * New theme
		 */
		public function new_category() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/new-category-ajax-action.php' ) );
		}

		/**
		 * Update theme
		 */
		public function update_category() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/update-category-ajax-action.php' ) );
		}

		/**
		 * New template
		 */
		public function new_template() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/new-template-ajax-action.php' ) );
		}

		/**
		 * Update template
		 */
		public function update_template() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/update-template-ajax-action.php' ) );
		}

		/**
		 * Reply ticket
		 */
		public function reply_ticket() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/reply-ticket-ajax-action.php' ) );
		}

		/**
		 * Save rating
		 */
		public function save_rating() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/save-rating-ajax-action.php' ) );
		}

		/**
		 * Delete template
		 */
		public function delete_template() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/delete-template-ajax-action.php' ) );
		}

		/**
		 * Delete customer
		 */
		public function delete_customer() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/delete-customer-ajax-action.php' ) );
		}

		/**
		 * Delete purchase
		 */
		public function delete_purchase() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/delete-purchase-ajax-action.php' ) );
		}

		/**
		 * Delete supporter
		 */
		public function delete_supporter() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/delete-supporter-ajax-action.php' ) );
		}

		/**
		 * Delete ticket
		 */
		public function delete_ticket() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/delete-ticket-ajax-action.php' ) );
		}

		/**
		 * Delete theme
		 */
		public function delete_category() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/delete-category-ajax-action.php' ) );
		}

		/**
		 * Delete message
		 */
		public function delete_message() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/delete-message-ajax-action.php' ) );
		}

		/**
		 * Paginator for theme
		 */
		public function category_listing_paginator() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/category-paginator-ajax-action.php' ) );
		}

		/**
		 *Get message for edit
		 */
		public function get_message() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/get-message-ajax-action.php' ) );
		}

		/**
		 * Auto send
		 */
		public function auto_sending() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/auto-sending-ajax-action.php' ) );
		}

		/**
		 * Lock process when click form reply
		 */
		public function process_get_form_reply() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/get-form-reply-ticket-ajax-action.php' ) );
		}

		/**
		 * Unclock process when click cancel reply
		 */
		public function process_cancel_form_reply() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/process-cancel-form-reply-ajax-action.php' ) );
		}

		/**
		 * Ticket details auto sending
		 */
		public function ticket_details_auto_sending() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/ticket-detail-auto-send-ajax-action.php' ) );
		}

		/**
		 * Update mail subject
		 */
		public function update_mail_subject() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/update-mail-template-ajax-action.php' ) );
		}

		/**
		 * Lock theme template
		 */
		public function lock_category() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/lock-category-ajax-action.php' ) );
		}

		/*
		 * Unlock theme
		 */
		public function unlock_category() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/unlock-category-ajax-action.php' ) );
		}

		/**
		 * Report filter
		 */
		public function report_filter() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/report-filter-ajax-action.php' ) );
		}

		/**
		 * Unlock user
		 */
		public function unlock_user() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/unlock-user-ajax-action.php' ) );
		}

		/**
		 * Get template for reply
		 */
		public function get_template() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/get-template-ajax-action.php' ) );
		}

		/**
		 * Templates pagination
		 */
		public function template_listing_paginator() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/template-listing-paginator-ajax-action.php' ) );
		}

		/**
		 * Set private template
		 */
		public function lock_template() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/lock-template-ajax-action.php' ) );
		}

		/**
		 * Set public template
		 */
		public function unlock_template() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/unlock-template-ajax-action.php' ) );
		}

		/**
		 * Supporter report filter
		 */
		public function supporter_report_filter() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/supporter-report-filter-ajax-action.php' ) );
		}

		/**
		 * Load notification
		 */
		public function get_notification() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/get-notification-ajax-action.php' ) );
		}

		/**
		 * Paginator for notes at dashboard
		 */
		public function note_listing_paginator() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/paginator-notes-ajax-action.php' ) );
		}

		/**
		 * Filter ticket for ajax
		 */
		public function dashboards_filter_ticket() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/dashboards-filter-ticket-ajax-action.php' ) );
		}

		/**
		 * Supporter report show message details
		 */
		public function supporter_report_show_message() {
			STS()->load_file( STS()->plugin_dir( 'inc/ajax/supporter-report-show-message-ajax-action.php' ) );
		}
	}
}