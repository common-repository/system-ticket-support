<?php
$mail_subjects = STS()->db()->mail_template()->get_all_template_mail();
?>
<div class="sts-page-main">
	<div class="sts-page-banner">
		<div class="container-fluid">
			<div class="sts-page-banner-content banner-center">
				<h3 class="sts-page-title">
					<?php
					esc_html_e( 'Manage mail templates', 'sts' )
					?>
				</h3>
			</div>
			<div class="form__message"></div>
		</div>
	</div>
	<div class="sts-page-container">
		<div class="container-fluid">
			<div class="sts-page-content-main">
				<table class="table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'ID', 'sts' ) ?></th>
							<th><?php esc_html_e( 'Subject', 'sts' ) ?></th>
							<th><?php esc_html_e( 'Action', 'sts' ) ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( $mail_subjects ):
							foreach ( $mail_subjects as $mail_subject ):
								?>
								<tr id="tr-<?php echo esc_attr( $mail_subject->id ) ?>">
									<td data-mobile-label="<?php esc_attr_e( 'ID', 'sts' ) ?>">
										<?php echo esc_html( $mail_subject->id ) ?>
									</td>
									<td data-mobile-label="<?php esc_attr_e( 'Subject', 'sts' ) ?>">
										<?php echo esc_html( $mail_subject->subject ); ?>
									</td>
									<td data-mobile-label="<?php esc_attr_e( 'Action', 'sts' ) ?>">
										<a class="action edit"
										   title="<?php esc_attr_e( 'Edit', 'sts' ) ?>"
										   href="<?php echo esc_url( sts_support_page_url( 'update-email-template/?id=' . $mail_subject->id ) ) ?>">
											<span class="dashicons dashicons-edit"></span>
										</a>
									</td>
								</tr>
							<?php endforeach;
						else: ?>
							<tr>
								<td colspan="100%"><?php esc_html_e( 'No mail template found!!', 'sts' ) ?> </td>
							</tr>
						<?php endif ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
