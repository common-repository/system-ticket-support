<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head() ?>
</head>
<body <?php body_class() ?>>
<?php $current_user = wp_get_current_user();
$is_lock = get_user_meta($current_user->ID, 'sts_is_lock', true);
?>
<div class="sts-page-content">
    <?php
    if (!$current_user->exists() || $is_lock == 1) {
        STS()->get_template("sts-header.php");
    } else {
    STS()->get_template("left-nav.php");
    ?>
    <div class="sts-page-wrapper">
<?php
STS()->get_template("sts-header-login.php");
}
