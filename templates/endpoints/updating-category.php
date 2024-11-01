<?php
$id    = sanitize_text_field( $_GET['cat_id'] );
$theme = STS()->db()->themes()->getting_theme_by_theme_id( $id );
?>
<div class="sts-page-main">
    <div class="sts-page-banner">
        <div class="container-fluid">
            <div class="sts-page-banner-content banner-center">
                <h3 class="sts-page-title">
					<?php
					esc_html_e( 'Edit category', 'sts' )
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
                    <form action="" method="post" id="form-update-theme"
                          data-sts-form-action="sts_update_category" data-sts-ladda="true">
						<?php wp_nonce_field( 'processing-update-category', 'sts_update_category_nonce_field' ); ?>
                        <input type="hidden" name="id" value="<?php echo esc_attr( $theme->id ) ?>">

                        <div class="form-control-group">
                            <label for="theme-name" class="form-control-label">
								<?php esc_html_e( 'Category name*', 'sts' ) ?>
                            </label>
                            <input id="theme-name" class="form-control" name="themeName" type="text"
                                   value="<?php echo esc_attr( $theme->theme_name ) ?>" required>

                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-nametag"></span>
                            </div>
                        </div>
                        <div class="form-control-group">
                            <label for="theme-name" class="form-control-label">
								<?php esc_html_e( 'Category ID*', 'sts' ) ?>
                            </label>
                            <input id="theme-name" class="form-control" name="themeId" type="text"
                                   value="<?php echo esc_attr( $theme->theme_id ) ?>" required>
                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-admin-network"></span>
                            </div>
                        </div>
                        <div class="form-button-group">
                            <div class="form-button-group-item">
                                <button class="btn btn-primary" type="submit">
									<?php esc_html_e( 'Update category', 'sts' ) ?>
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
