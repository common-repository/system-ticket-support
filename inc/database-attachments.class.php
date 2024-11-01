<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('STS_Database_Attachments')) {
    class STS_Database_Attachments
    {
        private static $_instance;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * @param $message_id
         * @return array|null|object
         */
        function get_attachment_by_message($message_id){
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$table_prefix}attachment WHERE message_id=%d",
                $message_id
            ));
        }

        /**
         * @param $user_id
         * @return array|null|object|void
         */
        function get_user_attachment_by_user_id($user_id){
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_prefix}user_attachment WHERE user_id=%d",
                $user_id
            ));
        }

    }
}