<div class="sts-page-main">
    <div class="sts-page-banner">
        <div class="container-fluid">
            <div class="sts-page-banner-content banner-center">
                <h3 class="sts-page-title">
					<?php
					esc_html_e( 'Add new category', 'sts' )
					?>
                </h3>
            </div>
            <div class="form__message"></div>
        </div>
    </div>
    <div class="sts-page-container">
        <div class="container-fluid">
            <div class="sts-page-content-main">
                <div class="sts-page-profile-header">
                    <span class="sts-page-profile-header-item"><?php esc_html_e( 'Category information', 'sts' ) ?></span>
                </div>
                <div class="page-form">
                    <form action="" method="post" id="form-new-theme" data-sts-form-action="sts_new_theme"
                          data-sts-ladda="true">
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
                            <label for="theme-id" class="form-control-label">
								<?php esc_html_e( 'Category ID*', 'sts' ) ?>
                            </label>
                            <input id="theme-id" class="form-control" name="themeId" type="text" required>
                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-admin-network"></span>
                            </div>
                        </div>
                        <div class="form-button-group">
                            <div class="form-button-group-item">
                                <button class="btn btn-primary" type="submit">
									<?php esc_html_e( 'Add category', 'sts' ) ?>
                                </button>
                            </div>
                            <div class="form-button-group-item">
                                <a href="<?php echo esc_url( sts_support_page_url( 'categories' ) ) ?>"
                                   class="btn btn-default">
									<?php esc_html_e( 'Come back', 'sts' ) ?>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
