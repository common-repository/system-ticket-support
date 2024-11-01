<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('STS_Database_Notification')) {
    class STS_Database_Notification
    {
        private static $_instance;
        public $limit_notification = 20;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * @param $user_id
         * @return string|null
         */
        public function count_notification_by_user($user_id)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_var($wpdb->prepare(
                "SELECT count(*) FROM {$table_prefix}notification n INNER JOIN {$table_prefix}user_notification u on n.id=u.notification_id WHERE u.user_id=%d ORDER BY created_date DESC",
                $user_id
            ));
        }

        public function get_notification_by_user($user_id, $offset)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$table_prefix}notification n INNER JOIN {$table_prefix}user_notification u on n.id=u.notification_id WHERE u.user_id=%d ORDER BY created_date DESC limit {$this->limit_notification} offset %d",
                $user_id, $offset
            ));
        }

        /**
         * Count number of notification has not read
         *
         * @param $user_id
         * @return null|string
         */
        public function count_notification_not_read($user_id)
        {
            global $wpdb;
            $table_prefix = STS()->db()->table_prefix();
            return $wpdb->get_var($wpdb->prepare(
                "SELECT count(DISTINCT u.id) FROM {$table_prefix}notification n INNER JOIN {$table_prefix}user_notification u on n.id=u.notification_id WHERE u.user_id=%d and u.is_read=0",
                $user_id
            ));
        }

        /**
         * @param $content
         * @param $user_ids
         * @param $ticket_id
         */
        public function sts_send_notification($content, $user_ids, $ticket_id)
        {
            global $wpdb;
            $date = date("Y-m-d H:i:s");
            $current_user = wp_get_current_user();
            $ticket = STS()->db()->tickets()->get_ticket_by_id($ticket_id);
            $table_prefix = STS()->db()->table_prefix();
            $table_name = $table_prefix . 'notification';
            $wpdb->insert(
                $table_name,
                array(
                    'content' => $content,
                    'link' => sts_link_ticket($ticket->id),
                )
            );
            $notificationId = $wpdb->insert_id;
            $table_name = $table_prefix . 'user_notification';
            foreach ($user_ids as $userID) {
                if ($current_user->ID !== intval($userID)) {
                    $wpdb->insert(
                        $table_name,
                        array(
                            'notification_id' => $notificationId,
                            'user_id' => $userID,
                            'created_date' => $date,
                            'is_read' => false
                        )
                    );
                }
            }
        }

    }
}