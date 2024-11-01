<h2><?php esc_html_e( 'Add a new menu', 'sts' ) ?></h2>
<form action="" method="post">
	<?php wp_nonce_field( 'sts_support_footer_setting_action', 'sts_support_footer_setting_nonce' ) ?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="menu-title"><?php esc_html_e( 'Title of menu', 'sts' ) ?></label>
				</th>
				<td>
					<input name="menu_title" id="menu-title" required
					       class="regular-text code" type="text">

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
					<input name="menu_link" id="menu-link" class="regular-text code" type="text" required>

					<p class="description" id="tagline-description">
						<?php esc_html_e( 'Input link for menu.', 'sts' ) ?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php submit_button( esc_html__( 'Save', 'sts' ), 'primary' ) ?>
</form>