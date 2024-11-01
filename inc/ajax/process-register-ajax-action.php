<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$error = array();
if (isset($_POST['sts_register_nonce_field']) &&
    wp_verify_nonce($_POST['sts_register_nonce_field'], 'processing-register')
) {
    global $wpdb;
    $table_prefix = STS()->db()->table_prefix();
    $firstName = sanitize_text_field($_POST['firstName']);
    $lastName = sanitize_text_field($_POST['lastName']);
    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);
    $rppassword = sanitize_text_field($_POST['rppassword']);
    $target = '.form__message';
    $result = sts_processing_register($firstName, $lastName, $email, $password, $rppassword);
    $error_arr = $result['error'];
    if (count($error_arr) > 0) {
        foreach ($error_arr as $item) {
            $error[] = $item;
        }
    }
} else {
    $error_message = esc_html__('Error!', 'sts');
    $error[] = array('message' => $error_message, 'target' => '.form__message');
}
if (count($error) == 0) {
    sts_send_json(
        array(
            'target' => '.form__message',
            'message' => esc_html__('You have register success. Please confirm your email to verify your account!!', 'sts'),
            'url' => sts_support_page_url()
        ),
        'alert-success');
} else {
    sts_send_json(
        array(
            'error_arr' => $error
        ),
        'alert-multi');
}