<div class="message-action__buttons">
	<?php if ( $is_question == 0 ): ?>
        <a href="#"
           data-sts-ladda="true"
           data-sts-action="sts_delete_message"
           data-sts-action-param="<?php echo esc_attr( json_encode( array(
			   'id'        => $message_id,
			   'custom_id' => $ticket_id,
			   'nonce'     => $nonce
		   ) ) ) ?>"
           data-sts-confirm="<?php esc_attr_e( 'Are you sure delete this message?', 'sts' ) ?>"
           class="message-action__item delete sts-delete-message"
           title="<?php esc_attr_e( 'Delete', 'sts' ) ?>">
            <span class="dashicons dashicons-trash"></span>
			<?php esc_html_e( 'Delete', 'sts' ) ?>
        </a>
	<?php endif; ?>
    <a href="#"
       id="button-<?php echo esc_attr( $message_id ) ?>"
       data-sts-ladda="true"
       data-sts-action="sts_get_message"
       data-sts-action-param="<?php echo esc_attr( json_encode( array(
		   'id'        => $message_id,
		   'ticket_id' => $ticket_id,
		   'nonce'     => $nonce_edit
	   ) ) ) ?>"
       data-sts-callback="STS.settingEditor"
       class="message-action__item message-action__item--edit"
       title="<?php esc_attr_e( 'Edit', 'sts' ) ?>">
        <span class="dashicons dashicons-edit"></span>
		<?php esc_html_e( 'Edit', 'sts' ) ?>
    </a>
</div>
