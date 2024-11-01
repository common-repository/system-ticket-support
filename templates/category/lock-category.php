<a class="action sts-lock-category ladda-button" href="#"
   data-sts-ladda="true"
   data-sts-action="sts_lock_category"
   data-sts-action-param="<?php echo esc_attr( json_encode( array(
	   'id'    => $theme_id,
	   'nonce' => wp_create_nonce( 'sts_frontend_lock_category' )
   ) ) ) ?>"

   data-sts-confirm="<?php esc_attr_e( 'Are your sure want to lock this category?', 'sts' ) ?>"
   data-sts-callback="STS.updateThemeStatus"
   title="<?php esc_attr_e( 'Lock', 'sts' ) ?>">
    <span class="dashicons dashicons-lock"></span>
</a>
