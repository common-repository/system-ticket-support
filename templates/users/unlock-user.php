<a href="#" class="edit"
   data-sts-ladda="true"
   data-sts-action="sts_unlock_user"
   data-sts-action-param="<?php echo esc_attr( json_encode( array(
	   'id'    => $user_id,
	   'nonce' => wp_create_nonce( 'sts_frontend_unlock_user' )
   ) ) ) ?>"
   data-sts-confirm="<?php esc_attr_e( 'Are you sure unlock this user?', 'sts' ) ?>"
   title="<?php esc_html_e( 'Unlock', 'sts' ) ?>">
    <span class="dashicons dashicons-unlock"></span>
</a>
