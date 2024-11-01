<?php
$current_user          = wp_get_current_user();
$user_meta             = get_userdata( $current_user->ID );
$user_roles            = $user_meta->roles;
if ( isset( $_GET['template_id'] ) && $_GET['template_id'] != '' ):
	$id = sanitize_text_field( $_GET['template_id'] );
	$template          = STS()->db()->templates()->getting_template_by_id( $id );
	if ( ( in_array( 'administrator', $user_roles ) ||
	       ( in_array( 'supporter', $user_roles ) && $template->user_id == $current_user->ID && $template->is_public == 0 )
	       || in_array( 'leader_supporter', $user_roles ) ) ):
		$template_tags_all = STS()->db()->templates()->get_all_template_tags();
		$template_tags = STS()->db()->templates()->get_template_tags_by_template_id( $template->id );
		?>
        <div class="sts-page-main">
            <div class="sts-page-banner">
                <div class="container-fluid">
                    <div class="sts-page-banner-content banner-center">
                        <h3 class="sts-page-title">
							<?php
							esc_html_e( 'Edit template', 'sts' )
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
                            <span class="sts-page-profile-header-item"><?php esc_html_e( 'Template information', 'sts' ) ?></span>

                        </div>
                        <div class="page-form">

                            <form method="post" action="" id="form-update-template" class="form-template"
                                  data-sts-form-action="sts_update_template" data-sts-ladda="true">
								<?php wp_nonce_field( 'processing-update-template', 'sts_update_temple_nonce_field' ); ?>

                                <div class="form-control-group">
                                    <label for="template-name" class="form-control-label">
										<?php esc_html_e( 'Template name*', 'sts' ) ?>
                                    </label>
                                    <input id="template-name" class="form-control" name="templateName"
                                           type="text" value="<?php echo esc_attr( $template->template_name ) ?>"
                                           required>

                                    <div class="form-control-group-icon">
                                        <span class="dashicons dashicons-nametag"></span>
                                    </div>
                                </div>
                                <input name="templateID" type="hidden"
                                       value="<?php echo esc_attr( $template->id ) ?>">

                                <div class="form-control-group form-control-editor">
                                    <textarea name="templateValue" id="template-reply"
                                              class="sts-text-editor form-control"><?php echo wpautop( wp_kses_post( $template->template_value ) ) ?></textarea>
                                </div>
                                <div class="form-control-group">
                                    <label for="template-tags" class="form-control-label">
										<?php esc_html_e( 'Template tags', 'sts' ) ?>
                                    </label>
                                    <select id="template-tags" class="form-control sts-template-tags"
                                            multiple="multiple"
                                            name="templateTags[]">
										<?php
										if ( $template_tags ):
											foreach ( $template_tags as $tag ):
												?>
                                                <option value="<?php echo esc_attr( $tag->tag ) ?>"
                                                        selected><?php echo esc_html( $tag->tag ) ?></option>
											<?php endforeach;
										endif;
										?>
                                    </select>
                                    <div class="form-control-group-icon">
                                        <span class="dashicons dashicons-tag"></span>
                                    </div>
                                </div>
								<?php if ( in_array( 'administrator', $user_roles )
								           || in_array( 'leader_supporter', $user_roles ) ): ?>
                                    <div class="form-control-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       name="is_public" <?php if ( $template->is_public == 1 ) {
													echo 'checked';
												} ?>>
												<?php esc_html_e( "Public template", "sts" ) ?>
                                            </label>
                                        </div>
                                    </div>
								<?php endif; ?>
                                <div class="form-button-group">
                                    <div class="form-button-group-item">
                                        <button class="btn btn-primary" type="submit">
											<?php esc_html_e( 'Update template', 'sts' ) ?>
                                        </button>
                                    </div>
                                    <div class="form-button-group-item">
                                        <a href="<?php echo esc_url( sts_support_page_url( 'templates' ) ); ?>"
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
	<?php
	endif;
endif;