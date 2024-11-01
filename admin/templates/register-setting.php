<?php
$content = get_option( 'sts_policy_register', '' );
?>
<h2><?php esc_html_e( 'Policy content for register', 'sts' ) ?></h2>
<form action="" method="post">
	<?php wp_nonce_field( 'sts_support_register_setting_action', 'sts_support_register_setting_nonce' ) ?>
    <div style="width: 50%">
		<?php
		wp_editor( wp_kses_post( $content ), 'sts-setting-register', array( 'textarea_rows' => 5 ) );
		?>
    </div>

    <p class="description" id="tagline-description">
		<?php esc_html_e( 'Add policy content for page register.', 'sts' ) ?>
    </p>
	<?php submit_button( esc_html__( 'Save', 'sts' ), 'primary' ) ?>
</form>

