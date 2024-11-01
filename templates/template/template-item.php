<?php foreach ( $templates as $template ): ?>
    <tr id="tr-<?php echo esc_attr( $template['id'] ) ?>">
        <td data-mobile-label="<?php esc_attr_e( 'ID', 'sts' ) ?>"
            class="sts-number-order">
			<?php echo esc_html( $template['id'] ) ?>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Template name', 'sts' ) ?>">
			<?php echo esc_html( $template['template_name'] ) ?>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Type of template', 'sts' ) ?>"
            class="sts-template-type-<?php echo esc_attr( $template['id'] ) ?>">
			<?php
			if ( $template['is_public'] == 1 ) {
				esc_html_e( 'Public', 'sts' );
			} else {
				esc_html_e( 'Private', 'sts' );
			}
			?>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Created user', 'sts' ) ?>">
			<?php echo esc_html( $template['user_name'] ) ?>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Action', 'sts' ) ?>">
			<?php if ( $template['can_edit'] == 1 ): ?>
                <a class="action edit"
                   title="<?php esc_attr_e( 'Edit', 'sts' ) ?>"
                   href="<?php echo esc_url( sts_support_page_url( 'update-template/?template_id=' . $template['id'] ) ); ?>">
                    <span class="dashicons dashicons-edit"></span>
                </a>
                <a class="action delete sts-delete-template"
                   data-sts-ladda="true"
                   data-sts-action="sts_delete_template"
                   data-sts-action-param="<?php echo esc_attr( json_encode( array(
					   'id' => $template['id'],
					   'nonce' => $nonce
				   ) ) ) ?>"
                   data-sts-confirm="<?php esc_attr_e( 'Are your sure want to delete this template?', 'sts' ) ?>"
                   title="<?php esc_attr_e( 'Delete', 'sts' ) ?>">
                    <span class="dashicons dashicons-trash"></span>
                </a>
			<?php endif; ?>
			<?php
			if ( $template['can_lock'] == 1 ): ?>
                <span id="sts-lock-template-<?php echo esc_attr( $template['id'] ) ?>">
                <?php if ( $template['is_public'] == 1 ) {
	                STS()->get_template( 'template/lock-template.php',
		                array(
			                'template_id' => $template['id'],
			                'nonce'       => wp_create_nonce( 'sts_frontend_lock_template' )
		                ) );
                } else {
	                STS()->get_template( 'template/unlock-template.php',
		                array(
			                'template_id' => $template['id'],
			                'nonce'       => wp_create_nonce( 'sts_frontend_unlock_template' )
		                ) );
                } ?>
            </span>
			<?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
