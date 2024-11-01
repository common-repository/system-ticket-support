<?php
$current_user = wp_get_current_user();
$limit        = STS()->db()->limit_user;
$numbCustomer = count( get_users( array( 'role' => 'subscriber' ) ) );
?>
<div class="sts-page-main listing-customer">
    <div class="sts-page-banner user-banner">
        <div class="container-fluid">
            <div class="sts-page-banner-content listing-customer-banner-content">
                <h3 class="sts-page-title">
					<?php
					esc_html_e( 'Manage customers', 'sts' )
					?>
                </h3>
                <div class="sts-block__filter">
                    <form action="" method="post" data-sts-form-action="sts_process_customer_filter"
                          data-sts-callback="STS.paginatorProcess"
                          id="form-paginator-listing-customer">
						<?php wp_nonce_field( 'sts_filter_customer_security', 'nonce' ); ?>
                        <input type="hidden" value="1" name="current_page" id="current-page"
                               class="current-page">
                        <div class="form-control-group">
							<?php
							do_action( 'sts_customer_top' );
							?>
                            <button type="submit" class="form-control-group-icon">
                                <span class="dashicons dashicons-search"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="form__message"></div>
        </div>
    </div>
    <div class="sts-page-container">
        <div class="container-fluid">
            <div class="sts-page-content-main">
                <table class="table customer-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'ID', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Name', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Email', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Number of ticket', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Action', 'sts' ) ?></th>
                        </tr>
                    </thead>
                    <tbody>
						<?php
						do_action('sts_customer_item')
						?>

                    </tbody>
                </table>
            </div>
            <div class="listing-customer-paginator">
				<?php
				if ( $numbCustomer > $limit ):
					$numbPage = ceil( $numbCustomer / $limit );
					?>
					<?php STS()->get_template( 'paginator.php', array(
					'numbPage'     => $numbPage,
					'offset'       => 0,
					'current_page' => 1,
					'target'       => '#form-paginator-listing-customer',
					'limit'        => $limit,
					'total'        => $numbCustomer
				) ) ?>

				<?php endif; ?>
            </div>
        </div>
    </div>
</div>