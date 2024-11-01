<?php for ( $i = 0; $i < count( $themes ); $i ++ ): ?>
    <tr id="tr-<?php echo esc_attr( $themes[ $i ]['id'] ) ?>">
        <td class="sts-number-order"
            data-mobile-label="<?php esc_attr_e( 'ID', 'sts' ) ?>"><?php echo esc_html( $themes[ $i ]['id'] ) ?></td>
        <td data-mobile-label="<?php esc_attr_e( 'Theme name', 'sts' ) ?>" class="sts-text-overflow">
            <a href="<?php echo esc_url( $themes[ $i ]['theme_link'] ) ?>"
               title="<?php echo esc_attr( $themes[ $i ]['theme_name'] ) ?>"
               target="_blank"> <?php echo esc_html( $themes[ $i ]['theme_name'] ); ?>
            </a>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Status', 'sts' ) ?>"
            class="sts-theme-status-<?php echo esc_attr( $themes[ $i ]['id'] ) ?>">
			<?php if ( $themes[ $i ]['status'] == 1 ): ?>
                <span class="sts-status sts-status-valid"><span class="dashicons dashicons-yes-alt"></span></span>
			<?php elseif ( $themes[ $i ]['status'] == 0 ): ?>
                <span class="sts-status sts-status-invalid"><span class="dashicons dashicons-dismiss"></span></span>
			<?php endif; ?>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Action', 'sts' ) ?>">
            <a class="action edit"
               title="<?php esc_attr_e( 'Edit', 'sts' ) ?>"
               href="<?php echo esc_url( sts_support_page_url( 'updating-theme/?theme_id=' . $themes[ $i ]['id'] ) ) ?>"><span class="dashicons dashicons-edit"></span></a>
			<?php
			if ( $themes[ $i ]['nb_ticket'] == 0 ):
				?>
                <a class="action delete sts-delete-theme ladda-button" href="#"
                   data-sts-ladda="true"
                   data-sts-action="sts_delete_theme"
                   data-sts-action-param="<?php echo esc_attr( json_encode( array(
					   'id'    => $themes[ $i ]['id'],
					   'nonce' => $nonce
				   ) ) ) ?>"

                   data-sts-confirm="<?php esc_attr_e( 'Are your sure want to delete this theme?', 'sts' ) ?>"
                   title="<?php esc_attr_e( 'Delete', 'sts' ) ?>">
                    <span class="dashicons dashicons-trash"></span>
                </a>
			<?php endif; ?>
            <span class="sts-unlock-theme-<?php echo esc_attr( $themes[ $i ]['id'] ) ?>">
                    <?php if ( $themes[ $i ]['status'] == 0 ):
	                    STS()->get_template( 'themes/unlock-theme.php', array( 'theme_id' => $themes[ $i ]['id'] ) );
                    else:
	                    STS()->get_template( 'themes/lock-theme.php', array( 'theme_id' => $themes[ $i ]['id'] ) );
                    endif; ?>
            </span>
        </td>
    </tr>
<?php
endfor;
