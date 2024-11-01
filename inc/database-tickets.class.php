<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Database_Tickets' ) ) {
	class STS_Database_Tickets {
		private static $_instance;
		public $limit_ticket = 20;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Get ticket by id
		 *
		 * @param $ticket_id
		 *
		 * @return array|null|object|void
		 */
		function get_ticket_by_id( $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}ticket WHERE id=%d",
				$ticket_id
			) );
		}

		/**
		 * @param $customer_id
		 * @param $theme_id
		 *
		 * @return array|object|null
		 */
		public function get_ticket_by_customer_all( $customer_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$tickets      = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$table_prefix}ticket WHERE customer_id=%d ORDER by updating_date,status",
					$customer_id
				)
			);


			return $tickets;
		}

		/**
		 * @param $customer_id
		 * @param $offset
		 *
		 * @return array|object|null
		 */
		public function get_ticket_by_customer_limit( $customer_id, $offset ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$table_prefix}ticket WHERE customer_id=%d ORDER by updating_date,status LIMIT {$this->limit_ticket} OFFSET %d",
					$customer_id, $offset
				)
			);
		}

		/**
		 * Get tickets have processed by supporter
		 *
		 * @param $supporter_id
		 * @param $status
		 * @param $offset
		 *
		 * @return array|object|null
		 */
		public function get_ticket_by_supporter( $supporter_id, $status, $offset ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$tickets      = array();
			if ( $status == '' ) {
				$tickets = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket WHERE supporter_id=%d ORDER by updating_date,status limit {$this->limit_ticket} offset %d",
					$supporter_id, $offset
				) );
			} elseif ( $status == 'open' ) {
				$tickets = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket WHERE supporter_id=%d and status <>3 ORDER by updating_date,status limit {$this->limit_ticket} offset %d",
					$supporter_id, $offset
				) );
			} elseif ( $status == 'close' ) {
				$tickets = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket WHERE supporter_id=%d and status =3 ORDER by updating_date,status limit {$this->limit_ticket} offset %d",
					$supporter_id, $offset
				) );
			}

			return $tickets;
		}

		/**
		 * Count number of ticket of supporter
		 *
		 * @param $supporter_id
		 *
		 * @return null|string
		 */
		public function count_ticket_by_supporter( $supporter_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_prefix}ticket WHERE supporter_id=%d",
				$supporter_id
			) );
		}

		/**
		 * Count number of ticket of supporter have status open
		 *
		 * @param $supporter_id
		 *
		 * @return null|string
		 */
		public function count_ticket_by_supporter_by_status_open( $supporter_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket WHERE supporter_id=%d and status <> %d",
				$supporter_id, 3
			) );
		}

		/**
		 * Count number of ticket of supporter have status close
		 *
		 * @param $supporter_id
		 *
		 * @return null|string
		 */
		public function count_ticket_by_supporter_by_status_close( $supporter_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket WHERE supporter_id=%d and status = %d",
				$supporter_id, 3
			) );
		}

		/**
		 * @param $status
		 * @param $keyword
		 * @param $ftstatus
		 * @param $theme
		 * @param $offset
		 * @param $user_id
		 * @param string $purchase
		 *
		 * @return array
		 */
		public function get_all_tickets_filter( $status, $keyword, $ftstatus, $theme, $offset, $user_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$keyword      = $wpdb->esc_like( $keyword );
			$search       = '%' . $keyword . '%';
			if ( ( $status == '' || $status == 1 ) && ( $ftstatus == '' || $ftstatus == 1 ) ) {
				$order = 't.status,t.updating_date';
			} else {
				$order = 't.updating_date desc,t.status';
			}
			$user_id_locked = sts_get_user_id_locked();
			if ( $status == '' ) {
				if ( $theme == '' && $ftstatus != '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket  WHERE subject LIKE %s and status=%d and customer_id not in ({$user_id_locked})",
						$search, $ftstatus ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.status=%d and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $ftstatus, $offset ) );
				} elseif ( $theme != '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and customer_id not in ({$user_id_locked})",
						$search, $theme ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $offset ) );
				} elseif ( $theme == '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE subject LIKE %s and customer_id not in ({$user_id_locked})",
						$search ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $offset ) );
				} else {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.status=%d and t.theme_id=%s and customer_id not in ({$user_id_locked})",
						$search, $ftstatus, $theme ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.status=%d and t.theme_id=%s and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $ftstatus, $theme, $offset ) );
				}

			} elseif ( $status == 'my-ticket-all' ) {
				if ( $theme == '' && $ftstatus != '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket  WHERE supporter_id=%d and subject LIKE %s and status=%d and customer_id not in ({$user_id_locked})",
						$user_id, $search, $ftstatus ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.supporter_id=%d and t.subject LIKE %s and t.status=%d and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$user_id, $search, $ftstatus, $offset ) );
				} elseif ( $theme != '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.supporter_id=%d and t.subject LIKE %s and t.theme_id=%s and customer_id not in ({$user_id_locked})",
						$user_id, $search, $theme ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.supporter_id=%d and t.subject LIKE %s and t.theme_id=%s and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$user_id, $search, $theme, $offset ) );
				} elseif ( $theme == '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE supporter_id=%d and subject LIKE %s and customer_id not in ({$user_id_locked})",
						$user_id, $search ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.supporter_id=%d and t.subject LIKE %s ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$user_id, $search, $offset ) );
				} else {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.supporter_id=%d and t.subject LIKE %s and t.status=%d and t.theme_id=%s and customer_id not in ({$user_id_locked})",
						$user_id, $search, $ftstatus, $theme ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.supporter_id=%d and t.subject LIKE %s and t.status=%d and t.theme_id=%s and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$user_id, $search, $ftstatus, $theme, $offset ) );
				}
			} elseif ( $status == "following" ) {
				if ( $theme == '' && $ftstatus != '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket  WHERE subject LIKE %s and status=%d and customer_id not in ({$user_id_locked}) and id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d)",
						$search, $ftstatus, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t  WHERE t.subject LIKE %s and t.status=%d and customer_id not in ({$user_id_locked}) and t.id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $ftstatus, $user_id, $offset ) );
				} elseif ( $theme != '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and customer_id not in ({$user_id_locked}) and t.id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d)",
						$search, $theme, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and customer_id not in ({$user_id_locked}) and t.id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $user_id, $offset ) );
				} elseif ( $theme == '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE subject LIKE %s and customer_id not in ({$user_id_locked}) and id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d)",
						$search, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and customer_id not in ({$user_id_locked}) and t.id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $user_id, $offset ) );
				} else {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.status=%d and customer_id not in ({$user_id_locked}) and t.id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d)",
						$search, $theme, $ftstatus, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.status=%d and customer_id not in ({$user_id_locked}) and t.id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $ftstatus, $user_id, $offset ) );
				}
			} elseif ( $status == 'processing' ) {
				if ( $theme == '' && $ftstatus != '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t  WHERE t.status<>3 and customer_id not in ({$user_id_locked}) and (t.id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (t.supporter_id is not null and t.id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0))) and t.subject LIKE %s and t.status=%d",
						$search, $ftstatus ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t inner join {$table_prefix}purchasecode p on t.purchasecode_id=p.id  WHERE t.status<>3 and customer_id not in ({$user_id_locked}) and (t.id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (t.supporter_id is not null and t.id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0))) and t.subject LIKE %s and t.status=%d  ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $ftstatus, $offset ) );
				} elseif ( $theme != '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.status<>3 and customer_id not in ({$user_id_locked}) and (t.id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (t.supporter_id is not null and t.id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0))) and t.subject LIKE %s and t.theme_id=%s",
						$search, $theme ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.status<>3 and customer_id not in ({$user_id_locked}) and (t.id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (t.supporter_id is not null and t.id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0))) and t.subject LIKE %s and t.theme_id=%s ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $offset ) );
				} elseif ( $theme == '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.status<>3 and customer_id not in ({$user_id_locked}) and (t.id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (t.supporter_id is not null and t.id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0))) and t.subject LIKE %s",
						$search ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.status<>3 and customer_id not in ({$user_id_locked}) and (t.id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (t.supporter_id is not null and t.id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0))) and t.subject LIKE %s  ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $offset ) );
				} else {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.status<>3 and customer_id not in ({$user_id_locked}) and (t.id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (t.supporter_id is not null and t.id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0))) and t.subject LIKE %s and t.theme_id=%s and t.status=%d",
						$search, $theme, $ftstatus ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.status<>3 and customer_id not in ({$user_id_locked}) and (t.id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (t.supporter_id is not null and t.id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0))) and t.subject LIKE %s and t.theme_id=%s and t.status=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $ftstatus, $offset ) );
				}
			} else {
				if ( $theme == '' && $ftstatus != '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket  WHERE subject LIKE %s and status=%d and customer_id not in ({$user_id_locked})",
						$search, $ftstatus ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.status=%d and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $ftstatus, $offset ) );
				} elseif ( $theme != '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and customer_id not in ({$user_id_locked})",
						$search, $theme ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $offset ) );
				} elseif ( $theme == '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE subject LIKE %s and customer_id not in ({$user_id_locked})",
						$search ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $offset ) );
				} else {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.status=%d and t.theme_id=%s and customer_id not in ({$user_id_locked})",
						$search, $ftstatus, $theme ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.status=%d and t.theme_id=%s and customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $ftstatus, $theme, $offset ) );
				}
			}

			return array( 'all_tickets' => $allTickets, 'tickets' => $tickets );
		}

		/**
		 * @param $status
		 * @param $offset
		 * @param $user_id
		 * @param string $theme_id
		 * @param string $purchase
		 *
		 * @return array
		 */
		public function get_all_tickets( $status, $offset, $user_id, $theme_id = '' ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			if ( $status == '' || $status == 1 ) {
				$order = 't.status,t.updating_date';
			} else {
				$order = 't.updating_date desc,t.status';
			}
			$user_id_locked = sts_get_user_id_locked();
			if ( $theme_id != '' ) {
				$allTickets = $wpdb->get_var( $wpdb->prepare(
					"SELECT count(*)  FROM {$table_prefix}ticket t where t.customer_id not in ({$user_id_locked}) and t.theme_id=%s", $theme_id ) );
				$tickets    = $wpdb->get_results( $wpdb->prepare(
					"SELECT t.* FROM {$table_prefix}ticket t where t.customer_id not in ({$user_id_locked}) and t.theme_id=%s ORDER by {$order} limit {$this->limit_ticket} offset %d", $theme_id
					, $offset ) );
			} else {
				if ( $status == '' ) {
					$allTickets = $wpdb->get_var(
						"SELECT count(*)  FROM {$table_prefix}ticket where customer_id not in ({$user_id_locked})" );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t where t.customer_id not in ({$user_id_locked}) ORDER by {$order} limit {$this->limit_ticket} offset %d"
						, $offset ) );

				} elseif ( $status == 'my-ticket-all' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE customer_id not in ({$user_id_locked}) and supporter_id=%d",
						$user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.customer_id not in ({$user_id_locked}) and t.supporter_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$user_id, $offset ) );
				} elseif ( $status == "following" ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE customer_id not in ({$user_id_locked}) and id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d)", $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.customer_id not in ({$user_id_locked}) and t.id in (SELECT ticket_id FROM {$table_prefix}user_follow_ticket WHERE user_id=%d) ORDER by {$order} limit {$this->limit_ticket} offset %d", $user_id, $offset )
					);
				} elseif ( $status == 'processing' ) {
					$allTickets = $wpdb->get_var(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE status<>3 and customer_id not in ({$user_id_locked}) and (id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (supporter_id is not null and id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0)) )" );
					$tickets    = $wpdb->get_results(
						$wpdb->prepare( "SELECT t.* FROM {$table_prefix}ticket t WHERE t.customer_id not in ({$user_id_locked}) and t.status<>3 and (t.id in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) or (t.supporter_id is not null and t.id not in (SELECT ticket_id FROM {$table_prefix}message WHERE is_question=0) )) ORDER by {$order} limit {$this->limit_ticket} offset %d", $offset ) );
				} else {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE customer_id not in ({$user_id_locked}) and status=%d", $status ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.customer_id not in ({$user_id_locked}) and t.status=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$status, $offset ) );
				}
			}

			return array( 'all_tickets' => $allTickets, 'tickets' => $tickets );
		}

		/**
		 * @param $status
		 * @param $keyword
		 * @param $ftstatus
		 * @param $user_id
		 * @param $theme
		 * @param $offset
		 * @param string $purchase
		 *
		 * @return array
		 */
		public function get_tickets_by_customer_filter( $status, $keyword, $ftstatus, $user_id, $theme, $offset ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$keyword      = $wpdb->esc_like( $keyword );
			$search       = '%' . $keyword . '%';
			if ( ( $status == '' || $status == 1 ) && ( $ftstatus == '' || $ftstatus == 1 ) ) {
				$order = 't.status,t.updating_date';
			} else {
				$order = 't.updating_date desc,t.status';
			}

			if ( $status == '' ) {
				if ( $theme == '' && $ftstatus != '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*) FROM {$table_prefix}ticket  WHERE subject LIKE %s and status=%d and customer_id=%d",
						$search, $ftstatus, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.status=%d and t.customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $ftstatus, $user_id, $offset ) );
				} elseif ( $theme != '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.customer_id=%d",
						$search, $theme, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $user_id, $offset ) );
				} elseif ( $theme == '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE subject LIKE %s and customer_id=%d",
						$search, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE subject LIKE %s and customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $user_id, $offset ) );
				} else {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.status=%d and t.customer_id=%d ",
						$search, $theme, $ftstatus, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.status=%d and t.customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $ftstatus, $user_id, $offset ) );
				}
			} else {
				if ( $theme == '' && $ftstatus != '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*) FROM {$table_prefix}ticket  WHERE subject LIKE %s and status=%d and customer_id=%d",
						$search, $ftstatus, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.status=%d and t.customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $ftstatus, $user_id, $offset ) );
				} elseif ( $theme != '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.customer_id=%d",
						$search, $theme, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $user_id, $offset ) );
				} elseif ( $theme == '' && $ftstatus == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE subject LIKE %s and customer_id=%d",
						$search, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $user_id, $offset ) );
				} else {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.status=%d and t.customer_id=%d ",
						$search, $theme, $ftstatus, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.subject LIKE %s and t.theme_id=%s and t.status=%d and t.customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$search, $theme, $ftstatus, $user_id, $offset ) );
				}
			}

			return array( 'all_tickets' => $allTickets, 'tickets' => $tickets );
		}


		/**
		 * @param $status
		 * @param $user_id
		 * @param $offset
		 * @param string $theme_id
		 * @param string $purchase
		 *
		 * @return array
		 */
		public function get_tickets_by_customer( $status, $user_id, $offset, $theme_id = '' ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			if ( $status == '' || $status == 1 ) {
				$order = 't.status,t.updating_date';
			} else {
				$order = 't.updating_date desc,t.status';
			}
			if ( $theme_id != '' ) {
				$allTickets = $wpdb->get_var( $wpdb->prepare(
					"SELECT count(*)  FROM {$table_prefix}ticket t  WHERE t.customer_id=%d and t.theme_id=%s", $user_id, $theme_id ) );
				$tickets    = $wpdb->get_results( $wpdb->prepare(
					"SELECT t.* FROM {$table_prefix}ticket t WHERE t.customer_id=%d and t.theme_id=%s ORDER by {$order} limit {$this->limit_ticket} offset %d", $user_id, $theme_id, $offset ) );

			} else {
				if ( $status == '' ) {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE customer_id=%d", $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d", $user_id, $offset ) );
				} else {
					$allTickets = $wpdb->get_var( $wpdb->prepare(
						"SELECT count(*)  FROM {$table_prefix}ticket WHERE status=%d and customer_id=%d", $status, $user_id ) );
					$tickets    = $wpdb->get_results( $wpdb->prepare(
						"SELECT t.* FROM {$table_prefix}ticket t WHERE t.status=%d and t.customer_id=%d ORDER by {$order} limit {$this->limit_ticket} offset %d",
						$status, $user_id, $offset ) );
				}
			}


			return array( 'all_tickets' => $allTickets, 'tickets' => $tickets );
		}

		/**
		 * @param $ticket_id
		 *
		 * @return array|null|object
		 */
		public function get_user_follow_ticket( $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$result_arr   = array();
			$user_follows = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM {$table_prefix}user_follow_ticket WHERE ticket_id=%d",
				$ticket_id
			) );
			if ( $user_follows ) {
				foreach ( $user_follows as $user_follow ) {
					$result_arr[] = $user_follow->user_id;
				}
			}

			return array_map( 'intval', $result_arr );
		}

		/**
		 * @param $current_status
		 * @param $status
		 * @param $search
		 * @param $from_date
		 * @param $to_date
		 * @param $supporter_id
		 * @param $limit
		 * @param $offset
		 *
		 * @return array
		 */
		public function filter_ticket_of_supporter( $current_status, $status, $search, $from_date, $to_date, $supporter_id, $limit, $offset ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			if ( $current_status == '' ) {
				if ( $status == '' ) {
					$ticketNum = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand supporter_id=%d",
						$search, $from_date, $to_date, $supporter_id
					) );
					$tickets   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand supporter_id=%d ORDER by updating_date,status limit {$limit} offset %d",
						$search, $from_date, $to_date, $supporter_id, $offset
					) );
				} else {
					$ticketNum = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand status=%d and supporter_id=%d",
						$search, $from_date, $to_date, $status, $supporter_id
					) );
					$tickets   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand supporter_id=%d and status=%d ORDER by updating_date,status limit {$limit} offset %d",
						$search, $from_date, $to_date, $supporter_id, $status, $offset
					) );
				}
			} else {
				if ( $status == '' ) {
					if ( $current_status == 'open' ) {
						$ticketNum = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand supporter_id=%d and status<>3",
							$search, $from_date, $to_date, $supporter_id
						) );
						$tickets   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand supporter_id=%d and status<>3 ORDER by updating_date,status limit {$limit} offset %d",
							$search, $from_date, $to_date, $supporter_id, $offset
						) );
					} else {
						$ticketNum = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand supporter_id=%d and status=3",
							$search, $from_date, $to_date, $supporter_id
						) );
						$tickets   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand supporter_id=%d and status=3 ORDER by updating_date,status limit {$limit} offset %d",
							$search, $from_date, $to_date, $supporter_id, $offset
						) );
					}
				} else {
					$ticketNum = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand status=%d and supporter_id=%d",
						$search, $from_date, $to_date, $status, $supporter_id
					) );
					$tickets   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket WHERE subject like %s and date(updating_date) between %s and %sand supporter_id=%d and status=%d ORDER by updating_date,status limit {$limit} offset %d",
						$search, $from_date, $to_date, $supporter_id, $status, $offset
					) );
				}
			}


			return array( 'tickets' => $tickets, 'numb_tickets' => $ticketNum );
		}

		/**
		 * @param $supporter_id
		 *
		 * @return array
		 */
		public function get_customer_id_by_supporter( $supporter_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_col( $wpdb->prepare( "SELECT distinct(customer_id) FROM {$table_prefix}ticket WHERE supporter_id=%d",
				$supporter_id
			) );
		}

		/**
		 * @param $customer_id
		 *
		 * @return array
		 */
		public function get_supporter_id_by_customer( $customer_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_col( $wpdb->prepare( "SELECT distinct(supporter_id) FROM {$table_prefix}ticket WHERE customer_id=%d",
				$customer_id
			) );
		}

		/**
		 * @return array
		 */
		public function get_all_ticket_id() {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_col( "SELECT id FROM {$table_prefix}ticket where status <>3" );
		}

		/**
		 * @param $customer_id
		 *
		 * @return array
		 */
		public function get_ticket_id_by_customer( $customer_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$table_prefix}ticket where supporter_id is null and status =1 and customer_id=%d", $customer_id ) );
		}

		/**
		 * @param $theme_id
		 *
		 * @return string|null
		 */
		public function count_ticket_by_theme( $theme_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where theme_id=%s", $theme_id ) );
		}


		/**
		 * @param $date
		 *
		 * @return array|object|null
		 */
		public function get_ticket_not_close( $date ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where status = 2 and DATEDIFF(current_date, updating_date)>=%d", $date ) );

		}

		public function get_ticket_locked() {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results( "SELECT * FROM {$table_prefix}ticket where is_lock = 1" );
		}

		/**
		 * @param $from_date
		 * @param $to_date
		 * @param $rating
		 * @param $supporter
		 * @param $offset
		 *
		 * @return array
		 */
		public function report( $from_date, $to_date, $rating, $supporter, $offset ) {
			global $wpdb;
			$table_prefix          = STS()->db()->table_prefix();
			$date                  = date( "Y-m-d H:i:s" );
			$nb_ticket_unsatisfied = 0;
			$nb_ticket_satisfied   = 0;
			if ( $from_date == '' ) {
				$from_date = date( "Y-m-d", mktime( date( 'H' ), date( 'i' ), date( 's' ), date( 'm' ), date( 'd' ) - 7, date( 'Y' ) ) );
			}
			if ( $to_date == '' ) {
				$to_date = $date;
			}
			if ( $rating == '' && $supporter == '' ) {
				$nb_ticket_satisfied   = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=1 and date(updating_date) between %s and %s", $from_date, $to_date ) );
				$nb_ticket_unsatisfied = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=0 and date(updating_date) between %s and %s", $from_date, $to_date ) );
			} elseif ( $rating != '' && $supporter == '' ) {
				if ( $rating == '1' ) {
					$nb_ticket_satisfied = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=1 and date(updating_date) between %s and %s", $from_date, $to_date ) );
				} else {
					$nb_ticket_unsatisfied = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=0 and date(updating_date) between %s and %s", $from_date, $to_date ) );
				}
			} elseif ( $rating == '' && $supporter != '' ) {
				if ( $supporter == '0' ) {
					$nb_ticket_satisfied   = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=1 and supporter_id is NULL and date(updating_date) between %s and %s", $from_date, $to_date ) );
					$nb_ticket_unsatisfied = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=0 and supporter_id is NULL and date(updating_date) between %s and %s", $from_date, $to_date ) );
				} else {
					$nb_ticket_satisfied   = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=1 and supporter_id=%d and date(updating_date) between %s and %s", $supporter, $from_date, $to_date ) );
					$nb_ticket_unsatisfied = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=0 and supporter_id=%d and date(updating_date) between %s and %s", $supporter, $from_date, $to_date ) );
				}
			} else {
				if ( $supporter == '0' ) {
					if ( $rating == '1' ) {
						$nb_ticket_satisfied = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=1 and supporter_id is NULL and date(updating_date) between %s and %s", $from_date, $to_date ) );
					} else {
						$nb_ticket_unsatisfied = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=0 and supporter_id is NULL and date(updating_date) between %s and %s", $from_date, $to_date ) );
					}

				} else {
					if ( $rating == '1' ) {
						$nb_ticket_satisfied = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=1 and supporter_id=%d and date(updating_date) between %s and %s", $supporter, $from_date, $to_date ) );
					} else {
						$nb_ticket_unsatisfied = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate=0 and supporter_id=%d and date(updating_date) between %s and %s", $supporter, $from_date, $to_date ) );
					}
				}
			}
			if ( $supporter == '0' ) {
				$tickets                      = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where rate=%d and date(updating_date) between %s and %s and supporter_id is NULL order by updating_date desc limit {$this->limit_ticket} offset %d", $rating, $from_date, $to_date, $offset ) );
				$nb_ticket_visited_not_rating = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate is null and is_rating_visited=1 and date(updating_date) between %s and %s and supporter_id is NULL", $from_date, $to_date ) );
				$tickets_visited_not_rating   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where rate is null and is_rating_visited=1 and date(updating_date) between %s and %s and supporter_id is NULL order by updating_date desc limit {$this->limit_ticket} offset %d", $from_date, $to_date, $offset ) );
				$nb_ticket_other              = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where status=3 and date(updating_date) between %s and %s and supporter_id is NULL and rate is NULL and is_rating_visited is NULL", $from_date, $to_date ) );
				$tickets_other                = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where status=3 and date(updating_date) between %s and %s and supporter_id is NULL and rate is NULL and is_rating_visited is NULL order by updating_date desc limit {$this->limit_ticket} offset %d", $from_date, $to_date, $offset ) );
			} elseif ( $supporter == '' ) {
				$tickets                      = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where rate=%d and date(updating_date) between %s and %s order by updating_date desc limit {$this->limit_ticket} offset %d", $rating, $from_date, $to_date, $offset ) );
				$nb_ticket_visited_not_rating = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate is null and is_rating_visited=1 and date(updating_date) between %s and %s", $from_date, $to_date ) );
				$tickets_visited_not_rating   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where rate is null and is_rating_visited=1 and date(updating_date) between %s and %s order by updating_date  desc limit {$this->limit_ticket} offset %d", $from_date, $to_date, $offset ) );
				$nb_ticket_other              = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where status=3 and date(updating_date) between %s and %s and rate is NULL and is_rating_visited is NULL", $from_date, $to_date ) );
				$tickets_other                = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where status=3 and date(updating_date) between %s and %s and rate is NULL and is_rating_visited is NULL order by updating_date desc limit {$this->limit_ticket} offset %d", $from_date, $to_date, $offset ) );
			} else {
				$tickets                      = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where rate=%d and date(updating_date) between %s and %s and supporter_id=%d order by updating_date desc limit {$this->limit_ticket} offset %d", $rating, $from_date, $to_date, $supporter, $offset ) );
				$nb_ticket_visited_not_rating = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where rate is null and is_rating_visited=1 and date(updating_date) between %s and %s and supporter_id=%d", $from_date, $to_date, $supporter ) );
				$tickets_visited_not_rating   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where rate is null and is_rating_visited=1 and date(updating_date) between %s and %s and supporter_id=%d order by updating_date desc limit {$this->limit_ticket} offset %d", $from_date, $to_date, $supporter, $offset ) );
				$nb_ticket_other              = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where status=3 and date(updating_date) between %s and %s and supporter_id=%d and rate is NULL and is_rating_visited is NULL", $from_date, $to_date, $supporter ) );
				$tickets_other                = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_prefix}ticket where status=3 and date(updating_date) between %s and %s and supporter_id=%d and rate is NULL and is_rating_visited is NULL order by updating_date desc limit {$this->limit_ticket} offset %d", $from_date, $to_date, $supporter, $offset ) );
			}

			return array(
				'satisfied'                    => $nb_ticket_satisfied,
				'unsatisfied'                  => $nb_ticket_unsatisfied,
				'tickets'                      => $tickets,
				'nb_ticket_visited_not_rating' => $nb_ticket_visited_not_rating,
				'tickets_visited_not_rating'   => $tickets_visited_not_rating,
				'nb_ticket_other'              => $nb_ticket_other,
				'tickets_other'                => $tickets_other
			);
		}

		/**
		 * @param $date
		 * @param $ticket_id
		 *
		 * @return false|int
		 */
		public function update_link_rating_visited( $date, $ticket_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$table_name   = $table_prefix . 'ticket';
			$result       = $wpdb->update( $table_name,
				array(
					'is_rating_visited' => 1,
					'visited_date'      => $date
				),
				array( 'id' => $ticket_id )
			);

			return $result;
		}

		/**
		 * @param $supporter_id
		 * @param $from_date
		 * @param $to_date
		 *
		 * @return string|null
		 */
		public function supporter_report( $supporter_id, $from_date, $to_date ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$date         = date( "Y-m-d H:i:s" );
			if ( $from_date == '' ) {
				$from_date = date( "Y-m-d" );
			}
			if ( $to_date == '' ) {
				$to_date = date( "Y-m-d" );
			}
			$nb_ticket = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$table_prefix}ticket where id in (SELECT ticket_id FROM {$table_prefix}message WHERE user_id=%d and date(created_date) between %s and %s)", $supporter_id, $from_date, $to_date ) );

			return $nb_ticket;
		}

		/**
		 * @param $ticket_id
		 */
		public function close_ticket( $ticket_id ) {
			global $wpdb;
			$date         = date( "Y-m-d H:i:s" );
			$table_prefix = STS()->db()->table_prefix();
			$table_name   = $table_prefix . 'ticket';
			$ticket       = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
			$result       = $wpdb->update( $table_name,
				array(
					'status'        => 3,
					'close_date'    => $date,
					'updating_date' => $date
				),
				array( 'id' => $ticket_id )
			);
			if ( $result !== false && $result !== 0 ) {
				$to_date        = date( "Y-m-d H:i:s", mktime( date( 'H' ), date( 'i' ), date( 's' ), date( 'm' ), date( 'd' ) + 7, date( 'Y' ) ) );
				$table_name_key = $table_prefix . 'key_mail_rate';
				$result         = $wpdb->insert( $table_name_key,
					array(
						'key_name'     => $date,
						'key_code'     => md5( $date ),
						'user_id'      => $ticket->customer_id,
						'ticket_id'    => $ticket->id,
						'date_expired' => $to_date
					)
				);
				if ( $result !== false ) {
					$is_receive_mail = get_metadata( 'user', $ticket->customer_id, 'sts_is_receive_mail', true );
					if ( $is_receive_mail == 1 ) {
						$customer = get_user_by( 'ID', $ticket->customer_id );
						$template = STS()->db()->mail_template()->get_mail_template_by_key( 'mail_close_ticket' );
						if ( $template ) {
							$subject = $template->subject;
							$message = sts_replace_content( $template->content,
								array(
									'%customer_name',
									'%ticket_url',
									'%user_close_ticket',
									'%rating_url',
									'%url_unsubscribe'
								),
								array(
									$customer->display_name,
									'<a href="' . esc_url( sts_link_ticket( $ticket->id ) ) . '" style="color:#039be5;">' . $ticket->subject . '</a>',
									'G5Plus support',
									'<a href="' . esc_url( sts_support_page_url( 'ticket-details/?t=' . $ticket->id . '&key=' . md5( $date ) . '#form-rating' ) ) . '" style="color:white;">' . esc_html__( 'Link for rating', 'sts' ) . '</a>',
									'<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $ticket->customer_id . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
								)
							);
						} else {
							$subject = 'Support rating';
							ob_start();
							STS()->get_template( 'mails/template-mail-rating.php',
								array(
									'customer_name'   => $customer->display_name,
									'ticket_url'      => '<a href="' . esc_url( sts_link_ticket( $ticket->id ) ) . '" style="color:#039be5;">' . $ticket->subject . '</a>',
									'user_name'       => 'G5Plus support',
									'rating_url'      => '<a href="' . esc_url( sts_support_page_url( 'ticket-details/?t=' . $ticket->id . '&key=' . md5( $date ) . '#form-rating' ) ) . '" style="color:white;">' . esc_html__( 'Link for rating', 'sts' ) . '</a>',
									'unsubscribe_url' => '<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $ticket->customer_id . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
								) );
							$message = ob_get_clean();
						}

						$sent = sts_send_mail( $customer->user_email, $subject, $message );
						if ( ! $sent ) {
							global $phpmailer;
							if ( isset( $phpmailer ) ) {
								error_log( $phpmailer->ErrorInfo );
							}
						}
					}
				}
			}
		}

		public function get_older_ticket( $user, $user_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$tickets      = array();
			if ( $user == 'all' ) {
				$tickets = $wpdb->get_results( "SELECT id,subject,updating_date FROM {$table_prefix}ticket where status=1 order by updating_date limit 5 offset 0 " );
			} elseif ( $user == 'my-ticket' ) {
				$tickets = $wpdb->get_results( $wpdb->prepare( "SELECT id,subject,updating_date FROM {$table_prefix}ticket where status=1 and supporter_id=%d order by updating_date limit 5 offset 0 ", $user_id ) );
			}

			return $tickets;
		}

		public function get_newest_responded_ticket_by_customer( $user_id ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();

			return $wpdb->get_results( $wpdb->prepare( "SELECT id,subject,updating_date FROM {$table_prefix}ticket where status=2 and customer_id=%d order by updating_date desc limit 5 offset 0 ", $user_id ) );

		}

		public function lock_ticket( $user_id, $ticket_id ) {
			$ticket = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
			$error  = '';
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$table_name   = $table_prefix . 'ticket';
			if ( $ticket->is_lock == 0 ) {
				$date   = date( "Y-m-d H:i:s" );
				$result = $wpdb->update( $table_name,
					array(
						'is_lock'   => 1,
						'user_lock' => $user_id,
						'lock_date' => $date
					),
					array( 'id' => $ticket_id )
				);
				if ( $result === false ) {
					$error = esc_html__( "Can not lock this ticket!!", 'sts' );
				}
			}

			return $error;
		}

		public function unlock_ticket( $ticket_id ) {
			$ticket = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
			$error  = '';
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$table_name   = $table_prefix . 'ticket';
			if ( $ticket->is_lock == 1 ) {
				$result = $wpdb->update( $table_name,
					array(
						'is_lock'   => 0,
						'user_lock' => null,
						'lock_date' => null
					),
					array( 'id' => $ticket_id )
				);
				if ( $result === false ) {
					$error = esc_html__( "Can not unlock this ticket!!", 'sts' );
				}
			}

			return $error;
		}
	}
}