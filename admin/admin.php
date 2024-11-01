<?php
/**
 * Admin functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'admin_menu', 'sts_support_menu_page' );
function sts_support_menu_page() {
	add_menu_page(
		esc_html__( 'Support Setting', 'sts' ),
		esc_html__( 'Support Setting', 'sts' ),
		'manage_options',
		'support-setting',
		'sts_support_setting' );
}


function sts_support_setting() {
	//Get the active tab from the $_GET param
	$default_tab = null;
	$tab         = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : $default_tab;
	?>
    <!-- Our admin page content should all be inside .wrap -->
    <div class="wrap">
    <!-- Print the page title -->
    <h1><?php esc_html_e( 'Support Setting', 'sts' ) ?></h1>

	<?php settings_errors( 'page_for_support_setting' ) ?>

    <!-- Here are our tabs -->
    <nav class="nav-tab-wrapper">
        <a href="?page=support-setting" class="nav-tab <?php if ( $tab === null ): ?>nav-tab-active<?php endif; ?>">
			<?php esc_html_e( 'Basic settings', 'sts' ) ?></a>
        <a href="?page=support-setting&tab=footer-settings"
           class="nav-tab <?php if ( $tab === 'footer-settings' ): ?>nav-tab-active<?php endif; ?>">
			<?php esc_html_e( 'Footer Settings', 'sts' ) ?>
        </a>
        <a href="?page=support-setting&tab=register-settings"
           class="nav-tab <?php if ( $tab === 'register-settings' ): ?>nav-tab-active<?php endif; ?>">
			<?php esc_html_e( 'Register Settings', 'sts' ) ?></a>
    </nav>

    <div class=" tab-content">
		<?php switch ( $tab ) :
			case 'footer-settings':
				STS()->get_plugin_template( 'admin/templates/footer-setting.php' );
				break;
			case 'register-settings':
				STS()->get_plugin_template( 'admin/templates/register-setting.php' );
				break;
			default:
				STS()->get_plugin_template( 'admin/templates/support-setting.php' );
				break;
		endswitch; ?>
    </div>
	<?php

}