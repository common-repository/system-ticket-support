<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('STS_Database_Key_Mail_Rate')) {
    class STS_Database_Key_Mail_Rate
    {
        private static $_instance;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        public function get_key_by_user_ticket_code($user_id, $ticket_id, $key_code)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_prefix}key_mail_rate WHERE user_id=%d and ticket_id=%d and key_code=%s",
                $user_id, $ticket_id, $key_code
            ));
        }
    }
}