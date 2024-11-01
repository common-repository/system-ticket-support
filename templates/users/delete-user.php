<a class="delete"
   id="customer-delete" href="#"
   data-sts-ladda="true"
   data-sts-action="<?php echo esc_attr( $action ) ?>"
   data-sts-action-param="<?php echo esc_attr( json_encode( array(
	   'id'    => $user_id,
	   'nonce' => $nonce
   ) ) ) ?>"
   data-sts-confirm="<?php esc_attr_e( 'Are you sure delete this user?', 'sts' ) ?>"

   title="<?php esc_attr_e( 'Delete', 'sts' ) ?>">
    <span class="dashicons dashicons-trash"></span>
</a>
