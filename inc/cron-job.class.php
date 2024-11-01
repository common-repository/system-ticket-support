<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'STS_Cron_Job' ) ) {
	class STS_Cron_Job {
		private static $_instance;

		public static function getInstance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {
			add_filter( 'cron_schedules', array( $this, 'ticket_cron_schedules' ) );
			add_filter( 'cron_schedules', array( $this, 'close_ticket_cron_schedules' ) );
			add_action( 'init', array( $this, 'run_task' ) );
			add_action( 'task_unlock_reply_hook', array( $this, 'unlock_reply_ticket' ) );
			add_action( 'task_close_responded_ticket_hook', array( $this, 'close_ticket' ) );
		}

		public function run_task() {
			if ( ! wp_next_scheduled( 'task_unlock_reply_hook' ) ) {
				wp_schedule_event( time(), 'unlockreplytime', 'task_unlock_reply_hook' );
			}
			if ( ! wp_next_scheduled( 'task_close_responded_ticket_hook' ) ) {
				wp_schedule_event( time(), 'closetickettime', 'task_close_responded_ticket_hook' );
			}
		}

		function ticket_cron_schedules( $schedules ) {
			$time = get_option( 'sts_cron_time' );
			if ( $time == ''|| $time == 0 ) {
				$time = 2;
			} else {
				$time = intval( $time );
			}
			if ( ! isset( $schedules["unlockreplytime"] ) ) {
				$schedules["unlockreplytime"] = array(
					'interval' => $time * 3600,
					'display'  => esc_html__( 'Every 2 hours', 'sts' ),
				);
			}

			return $schedules;
		}

		function unlock_reply_ticket() {
			error_log( 'Cron unlock reply is running' );
			$tickets = STS()->db()->tickets()->get_ticket_locked();
			if ( $tickets ) {
				$table_prefix = STS()->db()->table_prefix();
				$table_name   = $table_prefix . 'ticket';
				$date         = date( "Y-m-d H:i:s" );
				$time         = get_option( 'sts_cron_time' );
				if ( $time == '' || $time == 0 ) {
					$time = 2;
				}
				global $wpdb;
				foreach ( $tickets as $ticket ) {
					$diff  = date_diff( date_create( $ticket->lock_date ), date_create( $date ), true );
					$total = ( $diff->y * 365.25 + $diff->m * 30 + $diff->d ) * 24 + $diff->h + $diff->i / 60;
					if ( $total > intval( $time ) ) {
						$wpdb->update( $table_name,
							array(
								'is_lock'   => 0,
								'user_lock' => null,
								'lock_date' => null
							),
							array( 'id' => $ticket->id )
						);
					}
				}
			}
		}

		function close_ticket_cron_schedules( $schedules ) {
			if ( ! isset( $schedules["closetickettime"] ) ) {
				$schedules["closetickettime"] = array(
					'interval' => 86400,
					'display'  => esc_html__( 'Daily', 'sts' ),
				);
			}

			return $schedules;
		}

		function close_ticket() {
			error_log( 'Cron close ticket is running' );
			$time = get_option( 'sts_cron_time_close_ticket' );
			if ( $time == '' || $time == 0 ) {
				$time = 10;
			}
			$tickets = STS()->db()->tickets()->get_ticket_not_close( $time );
			if ( $tickets ) {
				foreach ( $tickets as $ticket ) {
					STS()->db()->tickets()->close_ticket( $ticket->id );
				}
			}
		}
	}
}