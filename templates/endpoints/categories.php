<?php
$numb_theme = count( STS()->db()->themes()->getting_theme_active() );
$limit      = STS()->db()->themes()->limit_theme;
?>
<div class="sts-page-main listing-themes">
    <div class="sts-page-banner">
        <div class="container-fluid">
            <div class="sts-page-banner-content banner-center">
                <h3 class="sts-page-title">
					<?php
					esc_html_e( 'Manage categories', 'sts' )
					?>
                </h3>
                <div class="banner-link">
                    <a href="#form-new-category"
                       data-href="<?php echo esc_url( sts_support_page_url( 'new-category' ) ) ?>"
                       class="btn btn-banner popup-with-form"><?php esc_html_e( 'Add new category', 'sts' ) ?>
                    </a>
                </div>
            </div>
            <div class="form__message"></div>
        </div>
    </div>
    <div class="sts-page-container">
        <div class="container-fluid">
            <div class="sts-page-content-main">
                <table class="table listing-theme">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Category ID', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Category name', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Status', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Action', 'sts' ) ?></th>
                        </tr>
                    </thead>
                    <tbody>
						<?php $themes = STS()->db()->themes()->get_theme_limit( 0 );
						if ( $themes ):
							$contents = array();
							for ( $i = 0; $i < count( $themes ); $i ++ ):
								$nb_ticket  = STS()->db()->tickets()->count_ticket_by_theme( $themes[ $i ]->theme_id );
								$contents[] = array(
									'id'         => $themes[ $i ]->id,
									'theme_name' => $themes[ $i ]->theme_name,
									'status'     => $themes[ $i ]->status,
									'theme_id'   => $themes[ $i ]->theme_id,
									'nb_ticket'  => $nb_ticket
								);
								?>
							<?php endfor;
							STS()->get_template( 'category/category-item.php',
								array(
									'themes' => $contents,
									'nonce'  => wp_create_nonce( 'sts_frontend_delete_category' )
								) );
						else: ?>
                            <tr>
                                <td colspan="100%"><?php esc_html_e( 'No category found!!', 'sts' ) ?> </td>
                            </tr>
						<?php endif ?>
                    </tbody>
                </table>
				<?php
				if ( $numb_theme > $limit ):
					$numbPage = ceil( $numb_theme / $limit );
					?>
                    <form method="post" action="" id="form-paginator-category"
                          data-sts-form-action="sts_category_listing_paginator"
                          data-sts-callback="STS.paginatorProcess">
                        <input type="hidden" value="1" name="current_page" id="current-page" class="current-page">
						<?php wp_nonce_field( 'sts_category_paginator_security', 'nonce' ); ?>
                    </form>
                    <div class="theme-paginator">
						<?php STS()->get_template( 'paginator.php', array(
							'numbPage'     => $numbPage,
							'offset'       => 0,
							'current_page' => 1,
							'target'       => '#form-paginator-category',
							'limit'        => $limit,
							'total'        => $numb_theme
						) ) ?>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </div>
</div>
<form action="" method="post" id="form-new-category" data-sts-form-action="sts_new_category"
      data-sts-ladda="true" class="form mfp-hide white-popup-block">
    <h3 class="form-title"><?php esc_html_e( 'New category', 'sts' ) ?></h3>

    <div class="form__message"></div>
	<?php wp_nonce_field( 'processing-new-category', 'sts_new_category_nonce_field' ); ?>
    <div class="form-control-group">
        <label for="theme-name" class="form-control-label">
			<?php esc_html_e( 'Category name*', 'sts' ) ?>
        </label>
        <input id="theme-name" class="form-control" name="themeName" type="text" required>
        <div class="form-control-group-icon">
            <span class="dashicons dashicons-nametag"></span>
        </div>
    </div>
    <div class="form-control-group">
        <label for="theme-name" class="form-control-label">
			<?php esc_html_e( 'Category ID*', 'sts' ) ?>
        </label>
        <input id="theme-name" class="form-control" name="themeId" type="text" required>
        <div class="form-control-group-icon">
            <span class="dashicons dashicons-admin-network"></span>
        </div>
    </div>
    <div class="form-button-group">
        <div class="form-button-group-item">
            <button class="btn btn-primary" type="submit">
				<?php esc_html_e( 'Add Category', 'sts' ) ?>
            </button>
        </div>
    </div>
</form>
