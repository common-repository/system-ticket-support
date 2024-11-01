<?php
// Get current support page id
$support_page_id        = sts_support_page_id();
$cron_time              = get_option( 'sts_cron_time','' );
$cron_time_close_ticket = get_option( 'sts_cron_time_close_ticket','' );
?>
<form action="" method="post">
	<?php wp_nonce_field( 'sts_support_setting_action', 'sts_support_setting_nonce' ) ?>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="support_page_id"><?php esc_html_e( 'Support Page', 'sts' ) ?></label>
                </th>
                <td>
					<?php echo sts_dropdown_pages( 'support_page_id', $support_page_id ) ?>
					<?php
					if ( $support_page_id != '' && $support_page_id > 0 ) {
						$url = get_page_link( $support_page_id );
						if ( $url !== false ) {
							echo wp_kses_post(sprintf(__('<a href="%s">This link</a> for your support page.','sts'),esc_url($url))) ;
						}
					}
					?>
                    <p class="description" id="tagline-description">
						<?php esc_html_e( 'Select an existing page for support panel.', 'sts' ) ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cron-time"><?php esc_html_e( 'Cron time remove lock reply ticket', 'sts' ) ?></label>
                </th>
                <td>
                    <input name="cron_time" id="cron-time" class="regular-text code" type="number"
                           value="<?php echo esc_attr( $cron_time ) ?>">

                    <p class="description" id="tagline-description">
						<?php esc_html_e( 'Input cron time for remove lock reply ticket unit hourly example 2,3.', 'sts' ) ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cron-time-close-ticket"><?php esc_html_e( 'Cron time close ticket', 'sts' ) ?></label>
                </th>
                <td>
                    <input name="cron_time_close_ticket" id="cron-time-close-ticket"
                           class="regular-text code" type="number"
                           value="<?php echo esc_attr( $cron_time_close_ticket ) ?>">

                    <p class="description" id="tagline-description">
						<?php esc_html_e( 'Input cron time for close ticket unit daily example 2,3.', 'sts' ) ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
	<?php submit_button( esc_html__( 'Save Changes', 'sts' ), 'primary' ) ?>
</form>
