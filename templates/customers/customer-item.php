<?php
for ( $i = 0; $i < count( $customers ); $i ++ ):?>
    <tr id="tr-<?php echo esc_attr( $customers[ $i ]['id'] ) ?>">
        <td class="sts-number-order"
            data-mobile-label="<?php esc_attr_e( 'ID', 'sts' ) ?>">
			<?php echo esc_html( $customers[ $i ]['id'] ) ?>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Name', 'sts' ) ?>" width="35%">
            <a class="table__link"
               href="<?php echo esc_url( sts_support_page_url( 'customer-details/?customer_id=' . $customers[ $i ]['id'] ) ); ?>">
                <img src="<?php echo esc_url( $customers[ $i ]['avatar'] ) ?>" class="customer-avatar"
                     alt="<?php esc_attr_e( 'Avatar', 'sts' ) ?>">
                <div class="customer_names">
                    <span class="customer-name"><?php echo esc_html( $customers[ $i ]['name'] ) ?></span>
                    <span class="customer-username">
						<?php echo esc_html( $customers[ $i ]['username'] ) ?></span>
                </div>

            </a>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Email', 'sts' ) ?>" width="30%">
			<?php echo esc_html( $customers[ $i ]['email'] ) ?>
            <div class="addition-info">
                <div class="addition-info__item">
                    <span class="addition-info__label"><?php esc_html_e( 'Register:', 'sts' ) ?></span>
                    <span class="addition-info__value"><?php echo esc_html( $customers[ $i ]['registered'] ) ?></span>
                </div>
            </div>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Number of ticket', 'sts' ) ?>">
			<?php echo esc_html( $customers[ $i ]['ticketNumb'] ) ?>
        </td>
        <td data-mobile-label="<?php esc_attr_e( 'Action', 'sts' ) ?>" class="table-action">
            <div class="action-items">
                <a class="action edit"
                   title="<?php esc_attr_e( 'View details', 'sts' ) ?>"
                   href="<?php echo esc_url( sts_support_page_url( 'customer-details/?customer_id=' . $customers[ $i ]['id'] ) ); ?>   ">
                    <span class="dashicons dashicons-info"></span>
                </a>
                <a class="action edit"
                   title="<?php esc_attr_e( 'Edit', 'sts' ) ?>"
                   href="<?php echo esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $customers[ $i ]['id'] . '&page=customers' ) ) ?>">
                    <span class="dashicons dashicons-edit"></span>
                </a>
                <div id="sts-lock-user-<?php echo esc_attr( $customers[ $i ]['id'] ) ?>"
                     class="action">
					<?php if ( $customers[ $i ]['is_lock'] == 0 ): ?>
                        <a class="action delete sts-delete-customer"
                           id="customer-delete" href="#"
                           data-sts-ladda="true"
                           data-sts-action="sts_delete_customer"
                           data-sts-action-param="<?php echo esc_attr( json_encode( array(
							   'id'    => $customers[ $i ]['id'],
							   'nonce' => wp_create_nonce( 'sts_frontend_delete_customer' )
						   ) ) ) ?>"
                           data-sts-confirm="<?php esc_attr_e( 'Are you sure delete this user?', 'sts' ) ?>"

                           title="<?php esc_attr_e( 'Delete', 'sts' ) ?>">
                            <span class="dashicons dashicons-trash"></span>
                        </a>
					<?php else: ?>
                        <a href="#" class="action edit"
                           data-sts-ladda="true"
                           data-sts-action="sts_unlock_user"
                           data-sts-action-param="<?php echo esc_attr( json_encode( array(
							   'id'    => $customers[ $i ]['id'],
							   'nonce' => wp_create_nonce( 'sts_frontend_unlock_user' )
						   ) ) ) ?>"
                           data-sts-confirm="<?php esc_attr_e( 'Are you sure unlock this user?', 'sts' ) ?>"
                           title="<?php esc_html_e( 'Unlock', 'sts' ) ?>">
                            <span class="dashicons dashicons-unlock"></span>
                        </a>
					<?php endif; ?>
                </div>
            </div>
        </td>
    </tr>
<?php endfor; ?>