<?php
$menus = get_option( 'sts_menu_footer' );
if ( $menus != false ):
	$menus = unserialize( $menus );
	?>
    <h2><?php esc_html_e( 'Listing of menu', 'sts' ) ?></h2>
    <table class="wp-list-table widefat fixed striped users">
        <thead>
            <tr>
                <th class="manage-column column-primary"><?php esc_html_e( 'Title', 'sts' ) ?></th>
                <th class="manage-column"><?php esc_html_e( 'Link', 'sts' ) ?></th>
            </tr>
        </thead>
        <tbody>
			<?php
			foreach ( $menus as $menu ):
				?>
                <tr>
                    <td class="manage-column column-primary">
						<?php echo esc_html( $menu['menu_title'] ) ?>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="?page=support-setting&tab=footer-settings&sts_action=edit&key=<?php echo esc_attr($menu['key']) ?>"><?php esc_html_e( 'Edit', 'sts' ) ?></a> |
                            </span>
                            <span class="delete">
                                <a href="?page=support-setting&tab=footer-settings&key=<?php echo esc_attr($menu['key'])?>&nonce=<?php echo wp_create_nonce( 'sts_delete_footer_menu' ) ?>"><?php esc_html_e( 'Delete', 'sts' ) ?></a>
                            </span>
                        </div>
                    </td>
                    <td class="manage-column"><?php echo esc_html( $menu['menu_link'] ) ?></td>
                </tr>
			<?php
			endforeach;
			?>
        </tbody>
    </table>
<?php
endif;
?>
<?php
if ( isset( $_GET['sts_action'] ) && $_GET['sts_action'] == 'edit' && isset( $_GET['key'] ) && $_GET['key'] != '' ) {
	$key   = sanitize_text_field($_GET['key']);
	$menus = get_option( 'sts_menu_footer' );
	if ( $menus != false ) {
		$nmenus = unserialize( $menus );
		$menu   = array();
		for ( $i = 0; $i < count( $nmenus ); $i ++ ) {
			if ( $nmenus[ $i ]['key'] == $key ) {
				$menu = $nmenus[ $i ];
				break;
			}
		}
		STS()->get_plugin_template( 'admin/templates/edit-menu.php', array( 'menu' => $menu ) );
	} else {
		?>
        <p></p>
        <a href="?page=support-setting&tab=footer-settings"
           class="button button-primary"><?php esc_html_e( 'Add a new menu', 'sts' ) ?></a>
		<?php
	}

} else {
	STS()->get_plugin_template( 'admin/templates/new-menu.php' );
}
?>
