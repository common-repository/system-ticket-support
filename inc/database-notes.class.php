<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'STS_Database_Notes' ) ) {
	class STS_Database_Notes {
		private static $_instance;
		public $limit_note = 10;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Get all note of ticket
		 *
		 * @param $ticket_id
		 *
		 * @return array|null|object
		 */
		public function get_note_by_ticket_id( $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}note WHERE ticket_id=%d",
				$ticket_id
			) );
		}

		/**
		 * @param $user_id
		 * @param $offset
		 *
		 * @return array|object|null
		 */
		public function get_notes_by_user( $user_id, $offset ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}note WHERE user_id=%d order by created_date desc limit {$this->limit_note} offset %d",
				$user_id, $offset
			) );
		}

		/**
		 * @param $user_id
		 *
		 * @return string|null
		 */
		public function count_notes_by_user( $user_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_var( $wpdb->prepare(
				"SELECT count(*) FROM {$table_prefix}note WHERE user_id=%d",
				$user_id
			) );
		}

	}
}