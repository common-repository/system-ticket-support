<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('STS_Database_Themes')) {
    class STS_Database_Themes
    {
        private static $_instance;
        public $limit_theme = 20;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Get all theme
         *
         * @return array|null|object
         */
        public function getting_theme_all()
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results("SELECT * FROM {$table_prefix}theme order by theme_name");
        }

        /**
         * @return array|object|null
         */
        public function getting_theme_active()
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results("SELECT * FROM {$table_prefix}theme WHERE status=1 order by theme_name");
        }

        /**
         * Get theme by id
         *
         * @param $theme_id
         * @return array|null|object|void
         */
        public function getting_theme_by_id($theme_id)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_prefix}theme WHERE theme_id=%s", $theme_id));
        }

        /**
         * Get theme by theme_id
         *
         * @param $theme_id
         * @return array|null|object|void
         */
        public function getting_theme_by_theme_id($id)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_prefix}theme WHERE id=%d", $id));
        }

        /**
         * @param $offset
         * @return array|object|null
         */
        public function get_theme_limit($offset)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_prefix}theme order by theme_name LIMIT {$this->limit_theme} OFFSET %d", $offset));
        }

        /**
         * @return array
         */
        public function get_theme_id()
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_col("SELECT theme_id FROM {$table_prefix}theme");
        }


    }
}