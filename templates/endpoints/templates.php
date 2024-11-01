<?php
$current_user = wp_get_current_user();
$user_meta    = get_userdata( $current_user->ID );
$user_roles   = $user_meta->roles;
if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ):

	if ( in_array( 'supporter', $user_roles ) ) {
		$templates   = STS()->db()->templates()->getting_template_by_user_limit( $current_user->ID, 0 );
		$nb_template = count( STS()->db()->templates()->getting_template_by_user( $current_user->ID ) );
	} else {
		$templates   = STS()->db()->templates()->get_all_template_limit( 0 );
		$nb_template = count( STS()->db()->templates()->get_all_template() );
	}
	$limit = STS()->db()->templates()->limit_template;
	?>
	<div class="sts-page-main">
		<div class="sts-page-banner">
			<div class="container-fluid">
				<div class="sts-page-banner-content banner-center">
					<h3 class="sts-page-title">
						<?php
						esc_html_e( 'Manage templates', 'sts' )
						?>
					</h3>
					<div class="banner-link">
						<a href="<?php echo esc_url( sts_support_page_url( 'new-template' ) ) ?>"
						   class="btn btn-banner"><?php esc_html_e( 'Add template', 'sts' ) ?>
						</a>
					</div>
				</div>
				<div class="form__message"></div>
			</div>
		</div>
		<div class="sts-page-container">
			<div class="container-fluid">
				<div class="sts-page-content-main">
					<table class="table table-templates">
						<thead>
							<tr>
								<th><?php esc_html_e( 'ID', 'sts' ) ?></th>
								<th><?php esc_html_e( 'Template name', 'sts' ) ?></th>
								<th><?php esc_html_e( 'Type of template', 'sts' ) ?></th>
								<th><?php esc_html_e( 'Created user', 'sts' ) ?></th>
								<th><?php esc_html_e( 'Action', 'sts' ) ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if ( $templates ):
								$contents = array();
								foreach ( $templates as $template ):
									$user      = get_user_by( 'ID', $template->user_id );
									$user_name = '';
									if ( $user ) {
										$user_name = $user->display_name;
									}
									$can_edit = 0;
									$can_lock = 0;
									if ( ( ( $template->is_public == 0 && $template->user_id == $current_user->ID ) && in_array( 'supporter', $user_roles ) )
									     || in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles )
									) {
										$can_edit = 1;
									}
									if ( in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ) {
										$can_lock = 1;
									}
									$contents[] = array(
										'id'             => $template->id,
										'template_name'  => $template->template_name,
										'template_value' => $template->template_value,
										'is_public'      => $template->is_public,
										'can_edit'       => $can_edit,
										'can_lock'       => $can_lock,
										'user_name'      => $user_name
									);
									?>
								<?php
								endforeach;
								STS()->get_template( 'template/template-item.php', array(
									'templates' => $contents,
									'nonce'     => wp_create_nonce( 'sts_frontend_delete_template' )
								) );
							else:?>
								<tr>
									<td colspan="100%"><?php esc_html_e( 'No template found!!', 'sts' ) ?> </td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
					<?php
					if ( $nb_template > $limit ):
						$numbPage = ceil( $nb_template / $limit );
						?>
						<form method="post" action="" id="form-paginator-listing-template" class="form close"
						      data-sts-form-action="sts_template_listing_paginator"
						      data-sts-callback="STS.paginatorProcess">
							<input type="hidden" value="1" name="current_page" id="current-page"
							       class="current-page">
							<?php wp_nonce_field( 'sts_template_paginator_security', 'nonce' ); ?>
						</form>
						<div class="listing-template-paginator">
							<?php STS()->get_template( 'paginator.php', array(
								'numbPage'     => $numbPage,
								'offset'       => 0,
								'current_page' => 1,
								'target'       => '#form-paginator-listing-template',
								'limit'        => $limit,
								'total'        => $nb_template
							) ) ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
<?php
endif;