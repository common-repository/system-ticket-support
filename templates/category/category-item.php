<?php for ( $i = 0; $i < count( $themes ); $i ++ ): ?>
    <tr id="tr-<?php echo esc_attr( $themes[ $i ]['id'] ) ?>">
        <td class="sts-number-order"
            data-mobile-label="<?php esc_attr_e( 'Category ID', 'sts' ) ?>"><?php echo esc_html( $themes[ $i ]['theme_id'] ) ?></td>
        <td data-mobile-label="<?php esc_attr_e( 'Category name', 'sts' ) ?>" class="sts-text-overflow">
			<?php echo esc_html( $themes[ $i ]['theme_name'] ); ?>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Status', 'sts' ) ?>"
            class="sts-category-status-<?php echo esc_attr( $themes[ $i ]['id'] ) ?>">
			<?php if ( $themes[ $i ]['status'] == 1 ): ?>
                <span class="sts-status sts-status-valid"><span class="dashicons dashicons-yes-alt"></span></span>
			<?php elseif ( $themes[ $i ]['status'] == 0 ): ?>
                <span class="sts-status sts-status-invalid"><span class="dashicons dashicons-dismiss"></span></span>
			<?php endif; ?>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Action', 'sts' ) ?>">
            <a class="action edit"
               title="<?php esc_attr_e( 'Edit', 'sts' ) ?>"
               href="<?php echo esc_url( sts_support_page_url( 'updating-category/?cat_id=' . $themes[ $i ]['id'] ) ) ?>"><span class="dashicons dashicons-edit"></span></a>
			<?php
			if ( $themes[ $i ]['nb_ticket'] == 0 ):
				?>
                <a class="action delete sts-delete-theme ladda-button" href="#"
                   data-sts-ladda="true"
                   data-sts-action="sts_delete_category"
                   data-sts-action-param="<?php echo esc_attr( json_encode( array(
					   'id'    => $themes[ $i ]['id'],
					   'nonce' => $nonce
				   ) ) ) ?>"

                   data-sts-confirm="<?php esc_attr_e( 'Are you sure want to delete this category?', 'sts' ) ?>"
                   title="<?php esc_attr_e( 'Delete', 'sts' ) ?>">
                    <span class="dashicons dashicons-trash"></span>
                </a>
			<?php endif; ?>
            <span class="sts-unlock-category-<?php echo esc_attr( $themes[ $i ]['id'] ) ?>">
                    <?php if ( $themes[ $i ]['status'] == 0 ):
	                    STS()->get_template( 'category/unlock-category.php', array( 'theme_id' => $themes[ $i ]['id'] ) );
                    else:
	                    STS()->get_template( 'category/lock-category.php', array( 'theme_id' => $themes[ $i ]['id'] ) );
                    endif; ?>
            </span>
        </td>
    </tr>
<?php
endfor;
