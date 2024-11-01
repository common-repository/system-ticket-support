<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Database_Messages' ) ) {
	class STS_Database_Messages {
		private static $_instance;
		public $limit_message = 10;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Get all message of ticket is answer
		 *
		 * @param $ticket_id
		 *
		 * @return array|null|object
		 */
		function get_all_message_answer( $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}message WHERE ticket_id=%d and is_question=0 ORDER BY created_date",
				$ticket_id
			) );
		}

		/**
		 * Get message of ticket is answer limit offset
		 *
		 * @param $ticket_id
		 *
		 * @return array|null|object
		 */
		function get_message_limit( $ticket_id, $offset ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM (SELECT * FROM {$table_prefix}message WHERE ticket_id=%d and is_question=0 ORDER BY created_date DESC LIMIT {$this->limit_message} OFFSET %d) as tp ORDER BY created_date ",
				$ticket_id, $offset
			) );
		}

		/**
		 * Get message is question
		 *
		 * @param $ticket_id
		 *
		 * @return array|null|object|void
		 */
		function get_question( $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}message WHERE ticket_id=%d and is_question=1",
				$ticket_id ) );
		}

		/**
		 * Count number of message is answer
		 *
		 * @param $ticket_id
		 *
		 * @return null|string
		 */
		function count_message_by_ticket_id( $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(message) FROM {$table_prefix}message  WHERE ticket_id = %s AND is_question=%d",
					$ticket_id,
					0
				)
			);
		}

		/**
		 * @param $ticket_id
		 *
		 * @return array
		 */
		function get_supporters_of_message( $ticket_id ) {
			global $wpdb;
			$table_prefix  = STS()->db()->table_prefix();
			$supporters    = get_users( array(
				'role__in' => array(
					'supporter',
					'leader_supporter',
					'administrator'
				)
			) );
			$supporter_ids = array();
			if ( $supporters ) {
				foreach ( $supporters as $supporter ) {
					$supporter_ids[] = $supporter->ID;
				}
			}
			$supporter_ids_str = implode( ',', $supporter_ids );
			$user              = $wpdb->get_row( $wpdb->prepare(
				"SELECT user_id,created_date FROM {$table_prefix}message  WHERE ticket_id = %s AND is_question=0 and user_id in ({$supporter_ids_str}) order by id",
				$ticket_id
			) );

			return $user;
		}

		/**
		 * @param $message_id
		 *
		 * @return array|object|void|null
		 */
		function get_message_by_id( $message_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}message WHERE id=%d",
				$message_id ) );
		}

		/**
		 * @param $ticket_id
		 *
		 * @return array|object|null
		 */
		public function get_all_message( $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}message WHERE ticket_id=%d order by id ",
				$ticket_id
			) );
		}

		/**
		 * @param $supporter_id
		 * @param $from_date
		 * @param $to_date
		 *
		 * @return string|null
		 */
		public function report_message_by_supporter( $supporter_id, $from_date, $to_date ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$date         = date( "Y-m-d H:i:s" );
			if ( $from_date == '' ) {
				$from_date = $from_date = date( "Y-m-d H:i:s", mktime( date( '00' ), date( '00' ), date( '00' ), date( 'm' ), date( 'd' ), date( 'Y' ) ) );
			}
			if ( $to_date == '' ) {
				$to_date = $date;
			}

			return $wpdb->get_var( $wpdb->prepare(
				"SELECT count(*) FROM {$table_prefix}message WHERE user_id=%d and date(created_date) between %s and %s",
				$supporter_id, $from_date, $to_date
			) );

		}

		public function get_message_by_supporter( $supporter_id, $from_date, $to_date, $offset ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}message WHERE user_id=%d and date(created_date) between %s and %s limit {$this->limit_message} offset %d",
				$supporter_id, $from_date, $to_date, $offset
			) );
		}

		function count_message_by_supporter( $supporter_id, $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_var( $wpdb->prepare(
				"SELECT count(*) FROM {$table_prefix}message WHERE user_id=%d and ticket_id=%d",
				$supporter_id, $ticket_id
			) );
		}

		function get_last_message( $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}message WHERE ticket_id=%d ORDER BY id DESC LIMIT 1", $ticket_id
			) );
		}
	}
}