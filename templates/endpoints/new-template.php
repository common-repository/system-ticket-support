<?php
$current_user = wp_get_current_user();
$user_meta    = get_userdata( $current_user->ID );
$user_roles   = $user_meta->roles;
?>
<div class="sts-page-main">
	<div class="sts-page-banner">
		<div class="container-fluid">
			<div class="sts-page-banner-content banner-center">
				<h3 class="sts-page-title">
					<?php
					esc_html_e( 'Add new template', 'sts' )
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
					<form method="post" action="" id="form-new-template" data-sts-form-action="sts_new_template"
					      data-sts-ladda="true" class="form-template">
						<?php wp_nonce_field( 'processing-new-template', 'sts_new_temple_nonce_field' ); ?>
						<div class="form-control-group">
							<label for="template-name" class="form-control-label">
								<?php esc_html_e( 'Template name*', 'sts' ) ?>
							</label>
							<input id="template-name" class="form-control" name="templateName" type="text" required>
							<div class="form-control-group-icon">
								<span class="dashicons dashicons-nametag"></span>
							</div>
						</div>
						<div class="form-control-group form-control-editor">
                                <textarea name="templateValue" id="templateReply"
                                          class="sts-text-editor"></textarea>
						</div>
						<div class="form-control-group">
							<label for="template-tags" class="form-control-label">
								<?php esc_html_e( 'Template tags', 'sts' ) ?>
							</label>
							<select id="template-tags" class="form-control sts-template-tags" multiple="multiple"
							        name="templateTags[]">
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
										       name="is_public">
										<?php esc_html_e( "Public template", "sts" ) ?>
									</label>
								</div>
							</div>
						<?php endif; ?>
						<div class="form-button-group">
							<div class="form-button-group-item">
								<button class="btn btn-primary" type="submit">
									<?php esc_html_e( 'Add template', 'sts' ) ?>
								</button>
							</div>
							<div class="form-button-group-item">
								<a href="<?php echo esc_url( sts_support_page_url( 'templates' ) ) ?>"
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
