<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('STS_Database_Templates')) {
    class STS_Database_Templates
    {
        private static $_instance;
        public $limit_template = 20;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * @param $user_id
         * @return array|object|null
         */
        public function getting_template_by_user($user_id)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_prefix}template where (user_id=%d and is_public=0) or is_public=1 order by template_name", $user_id));
        }

        /**
         * @param $user_id
         * @param $offset
         * @return array|object|null
         */
        public function getting_template_by_user_limit($user_id, $offset)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_prefix}template where (user_id=%d and is_public=0) or is_public=1 order by template_name limit {$this->limit_template} offset %d", $user_id, $offset));
        }

        /**
         * @return array|object|null
         */
        public function get_all_template()
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results("SELECT * FROM {$table_prefix}template order by template_name");
        }

        /**
         * @param $offset
         * @return array|object|null
         */
        public function get_all_template_limit($offset)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_prefix}template order by template_name limit {$this->limit_template} offset %d", $offset));
        }


        /**
         * Get template by id
         *
         * @param $id
         * @return array|null|object|void
         */
        public function getting_template_by_id($id)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_prefix}template WHERE id=%d", $id));
        }

        /**
         * @return array|object|null
         */
        public function get_all_template_tags()
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results("SELECT * FROM {$table_prefix}template_tags");

        }

        /**
         * @param $template_id
         * @return array|object|null
         */
        public function get_template_tags_by_template_id($template_id)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_prefix}template_tags WHERE template_id=%d", $template_id));
        }


        /**
         * @param $tag
         * @return string|null
         */
        public function get_tag_by_tag($tag)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_var($wpdb->prepare("SELECT tag FROM {$table_prefix}template_tags WHERE tag=%s", $tag));

        }

        public function get_tags_by_template_id($template_id)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_col($wpdb->prepare("SELECT tag FROM {$table_prefix}template_tags WHERE template_id=%d", $template_id));

        }


    }
}