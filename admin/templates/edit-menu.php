<?php
if ( ! empty( $menu ) ):
	?>
    <h2><?php esc_html_e( 'Edit menu', 'sts' ) ?></h2>
    <form action="" method="post">
		<?php wp_nonce_field( 'sts_support_footer_setting_edit_menu_action', 'sts_support_footer_setting__edit_menu_nonce' ) ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="menu-title"><?php esc_html_e( 'Title of menu', 'sts' ) ?></label>
                    </th>
                    <td>
                        <input name="menu_title" id="menu-title" required
                               class="regular-text code" type="text"
                               value="<?php echo esc_attr( $menu['menu_title'] ) ?>">

                        <p class="description" id="tagline-description">
							<?php esc_html_e( 'Add title for menu.', 'sts' ) ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="menu-link"><?php esc_html_e( 'Link of menu', 'sts' ) ?></label>
                    </th>
                    <td>
                        <input name="menu_link" id="menu-link" class="regular-text code" type="text" required
                               value="<?php echo esc_attr( $menu['menu_link'] ) ?>">

                        <p class="description" id="tagline-description">
							<?php esc_html_e( 'Input link for menu.', 'sts' ) ?>
                        </p>
                        <input type="hidden" name="key" value="<?php echo esc_attr( $menu['key'] ) ?>">
                    </td>
                </tr>
            </tbody>
        </table>
		<?php submit_button( esc_html__( 'Save', 'sts' ), 'primary' ) ?>
        <a href="?page=support-setting&tab=footer-settings"
           class="button button-back"><?php esc_html_e( 'Add a new menu', 'sts' ) ?></a>
    </form>
<?php
else:
	?>
    <p><?php esc_html_e( 'No have any menu!', 'sts' ) ?></p>
<?php
endif;
?>
