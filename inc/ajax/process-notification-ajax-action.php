<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$error = '';
if (isset($_POST['nonce']) && check_ajax_referer('sts_notification_security', 'nonce')) {
    global $wpdb;
    $table_prefix = STS()->db()->table_prefix();
    $current_user = wp_get_current_user();
    if (user_can($current_user, 'subscriber') || user_can($current_user, 'administrator')
        || user_can($current_user, 'supporter') || user_can($current_user, 'leader_supporter')) {
        $result = $wpdb->query($wpdb->prepare("UPDATE {$table_prefix}user_notification set is_read=%d where user_id=%d",
            1, $current_user->ID));
        if ($result === false) {
            $error = esc_html__("Have an error!", 'sts');
        }
    } else {
        $error = esc_html__("You have not permission to do this action", 'sts');
    }
} else {
    $error = esc_html__("Error", 'sts');
}
if ($error == '') {
    sts_send_json(array(
        'close_content' => '.notification-number'
    ), '');
} else {
    sts_send_json(
        array(
            'target' => '.form__message',
            'message' => $error
        ),
        'alert');
}