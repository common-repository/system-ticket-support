<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('STS_Database_Mail_Template')) {
    class STS_Database_Mail_Template
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
         * @param $key
         * @return string|null
         */
        public function get_subject_mail_by_key($key)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_var($wpdb->prepare(
                "SELECT subject FROM {$table_prefix}mail_template WHERE key_name=%s",
                $key
            ));
        }

        /**
         * @return array|object|null
         */
        public function get_all_template_mail()
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results(
                "SELECT * FROM {$table_prefix}mail_template order by created_date desc");
        }

        /**
         * @param $id
         * @return array|object|void|null
         */
        public function get_mail_template_by_id($id)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_prefix}mail_template WHERE id=%d",
                $id
            ));
        }

        /**
         * @param $key
         * @return array|object|null
         */
        public function get_mail_template_by_key($key)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_prefix}mail_template WHERE key_name=%s",
                $key
            ));
        }

    }
}