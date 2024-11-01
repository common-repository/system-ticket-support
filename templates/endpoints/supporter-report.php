<?php
$supporters = get_users( array( 'role__in' => array( 'supporter', 'leader_supporter', 'administrator' ) ) );
$date       = date( "Y-m-d" );
$from_date  = $date;
$to_date    = $date;
?>
<div class="sts-page-main supporter-report">
	<div class="sts-page-banner">
		<div class="container-fluid">
			<div class="sts-page-banner-content banner-center">
				<h3 class="sts-page-title">
					<?php
					esc_html_e( 'Supporter report', 'sts' )
					?>
				</h3>
			</div>
			<div class="form__message"></div>
		</div>
	</div>
	<div class="sts-page-container">
		<div class="container-fluid">
			<div class="sts-page-content-main">
				<div class="sts-block__filter">
					<div class="report__filter">
						<form method="post" action="" id="sts-form-supporter-report-filter"
						      data-sts-form-action="sts_supporter_report_filter"
						      data-sts-ladda="true">
							<?php wp_nonce_field( 'sts_supporter_report_filter_security', 'nonce' ); ?>
							<div class="supporter-report__filter-wrapper">
								<div class="supporter-report__form-control">
									<div class="form-control-group date">
										<label class="form-control-label" for="from-date">
											<?php esc_html_e( 'Form date', 'sts' ) ?>
										</label>
										<input class="form-control sts-datepicker" type="text" id="from-date"
										       name="fromDate" value="<?php echo esc_attr( $from_date ) ?>">
									</div>
								</div>
								<div class="supporter-report__form-control">
									<div class="form-control-group date">
										<label class="form-control-label" for="to-date">
											<?php esc_html_e( 'To date', 'sts' ) ?>
										</label>
										<input class="form-control sts-datepicker" type="text" id="to-date"
										       name="toDate" value="<?php echo esc_attr( $to_date ) ?>">
									</div>
								</div>
								<div class="supporter-report__filter-button">
									<button type="submit" class="btn btn-primary">
										<?php esc_html_e( 'Search', 'sts' ) ?>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<table class="table supporter-report-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Supporter', 'sts' ) ?></th>
							<th><?php esc_html_e( 'From date', 'sts' ) ?></th>
							<th><?php esc_html_e( 'To date', 'sts' ) ?></th>
							<th><?php esc_html_e( 'Number of message', 'sts' ) ?></th>
							<th><?php esc_html_e( 'Number of ticket', 'sts' ) ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( $supporters ):
							$contents = array();
							foreach ( $supporters as $supporter ) {
								$nb_message = STS()->db()->messages()->report_message_by_supporter( $supporter->ID, $from_date, $to_date );
								$nb_ticket  = STS()->db()->tickets()->supporter_report( $supporter->ID, $from_date, $from_date );
								$contents[] = array(
									'supporter_name' => $supporter->display_name,
									'from_date'      => $from_date,
									'to_date'        => $to_date,
									'nb_message'     => $nb_message,
									'nb_ticket'      => $nb_ticket,
									'supporter_id'   => $supporter->ID,
								);
							}
							STS()->get_template( 'reports/supporter-report-item.php',
								array(
									'contents'     => $contents,
									'current_page' => 1,
								) );
						else:?>
							<tr>
								<td colspan="100%"><?php esc_html_e( 'No data!!', 'sts' ) ?> </td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			<div class="sts-block block-listing-message close">
				<h3 class="sts-block__title"><?php esc_html_e( 'Listing message', 'sts' ) ?></h3>
				<div class="sts-block__container">
					<table class="table listing-message">
						<thead>
							<tr>
								<th><?php esc_html_e( 'ID', 'sts' ) ?></th>
								<th><?php esc_html_e( 'Message', 'sts' ) ?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<div class="listing-message-paginator"></div>
			</div>
		</div>

	</div>
</div>
