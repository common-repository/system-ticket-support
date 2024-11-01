<a class="action sts-lock-template edit"
   data-sts-ladda="true"
   data-sts-action="sts_lock_template"
   data-sts-action-param="<?php echo esc_attr( json_encode( array(
	   'id'    => $template_id,
	   'nonce' => $nonce
   ) ) ) ?>"
   data-sts-confirm="<?php esc_attr_e( 'Are your sure want to set private this template?', 'sts' ) ?>"
   data-sts-callback="STS.updateThemeStatus"
   title="<?php esc_attr_e( 'Set private template', 'sts' ) ?>">
    <span class="dashicons dashicons-lock"></span>
</a>
