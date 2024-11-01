<?php
$date                         = date( "Y-m-d" );
$from_date                    = date( "Y-m-d", mktime( date( 'H' ), date( 'i' ), date( 's' ), date( 'm' ), date( 'd' ) - 7, date( 'Y' ) ) );
$to_date                      = $date;
$supporters                   = get_users( array(
	'role__in' => array(
		'supporter',
		'leader_supporter',
		'administrator'
	)
) );
$nb_ticket                    = STS()->db()->tickets()->report( $from_date, $to_date, '', '', 0 );
$nb_ticket_satisfied          = $nb_ticket['satisfied'];
$nb_ticket_unsatisfied        = $nb_ticket['unsatisfied'];
$nb_ticket_visited_not_rating = $nb_ticket['nb_ticket_visited_not_rating'];
$tickets_visited_not_rating   = $nb_ticket['tickets_visited_not_rating'];
$nb_ticket_other              = $nb_ticket['nb_ticket_other'];
$total                        = $nb_ticket_satisfied + $nb_ticket_unsatisfied + $nb_ticket_visited_not_rating + $nb_ticket_other;
?>
<div class="sts-page-main report">
    <div class="sts-page-banner">
        <div class="container-fluid">
            <div class="sts-page-banner-content banner-center">
                <h3 class="sts-page-title">
					<?php
					esc_html_e( 'Rating report', 'sts' )
					?>
                </h3>
            </div>
            <div class="form__message"></div>
        </div>
    </div>
    <div class="sts-page-container">
        <div class="container-fluid">
            <div class="sts-page-content-main ">
                <div class="sts-block__filter">
                    <div class="report__filter">
                        <form method="post" action="" id="sts-form-report-filter"
                              data-sts-form-action="sts_report_filter"
                              data-sts-callback="STS.updateChartReport"
                              data-sts-ladda="true">
							<?php wp_nonce_field( 'sts_report_filter_security', 'nonce' ); ?>
                            <input type="hidden" name="rate_paginator" id="rate-paginator" value=""
                                   class="rate-paginator">
                            <div class="report__filter-wrapper">
                                <div class="form-control-group date">
                                    <label class="form-control-label" for="from-date">
										<?php esc_html_e( 'Form date', 'sts' ) ?>
                                    </label>
                                    <input class="form-control sts-datepicker" type="text" id="from-date"
                                           name="fromDate" value="<?php echo esc_attr( $from_date ) ?>">
                                </div>
                                <div class="form-control-group date">
                                    <label class="form-control-label" for="to-date">
										<?php esc_html_e( 'To date', 'sts' ) ?>
                                    </label>
                                    <input class="form-control sts-datepicker" type="text" id="to-date"
                                           name="toDate" value="<?php echo esc_attr( $to_date ) ?>">
                                </div>
                                <div class="form-control-group">
                                    <label class="form-control-label" for="rating">
										<?php esc_html_e( "Rating:", "sts" ) ?>
                                    </label>
                                    <select class="form-control" id="rating" name="rating">
                                        <option value=""><?php esc_html_e( 'All', 'sts' ) ?></option>
                                        <option value="<?php esc_attr_e( '1', 'sts' ) ?>"><?php esc_html_e( 'Satisfied', 'sts' ) ?></option>
                                        <option value="<?php esc_attr_e( '0', 'sts' ) ?>"><?php esc_html_e( 'Unsatisfied', 'sts' ) ?></option>
                                    </select>
                                </div>
                                <div class="form-control-group">
                                    <label class="form-control-label" for="supporter">
										<?php esc_html_e( "Supporter:", "sts" ) ?>
                                    </label>
                                    <select class="form-control" id="supporter" name="supporter">
                                        <option value=""><?php esc_html_e( 'All', 'sts' ) ?></option>
                                        <option value="<?php esc_attr_e( '0', 'sts' ) ?>"><?php esc_html_e( 'Not assign', 'sts' ) ?></option>
										<?php
										if ( $supporters ):
											foreach ( $supporters as $supporter ):
												?>
                                                <option value="<?php echo esc_attr( $supporter->ID ) ?>">
													<?php echo esc_html( $supporter->display_name ) ?>
                                                </option>
											<?php endforeach;
										endif; ?>
                                    </select>
                                </div>
                                <div class="sts-block__filter-button">
                                    <button type="submit" class="btn btn-primary">
										<?php esc_html_e( 'Search', 'sts' ) ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="report__chart">
                    <span id="sts-satisfied" hidden><?php echo esc_html( $nb_ticket_satisfied ) ?></span>
                    <span id="sts-unsatisfied" hidden><?php echo esc_html( $nb_ticket_unsatisfied ) ?></span>
                    <span id="sts-visited" hidden><?php echo esc_html( $nb_ticket_visited_not_rating ) ?></span>
                    <span id="sts-other" hidden><?php echo esc_html( $nb_ticket_other ) ?></span>
                    <div class="report__chart-chart">
						<?php
						if ( $total == 0 ):
							?>
                            <div class="text-center">
								<?php
								esc_html_e( 'Current no have ticket!', 'sts' );
								?>
                            </div>
						<?php
						else:
							?>
                            <canvas id="reportChart" height="237" width="475"></canvas>
						<?php
						endif;
						?>

                    </div>
					<?php
					if ( $total > 0 ):
						?>
                        <div class="report__label">
                                <span class="report__label-item" id="sts-report-legend-satisfied">
                                    <?php STS()->get_template( 'reports/satisfied-link.php',
	                                    array(
		                                    'nb_ticket_satisfied' => $nb_ticket_satisfied,
		                                    'form_date'           => '',
		                                    'to_date'             => '',
		                                    'supporter'           => ''
	                                    ) ); ?>
                                </span>
                            <span class="report__label-item" id="sts-report-legend-unsatisfied">
                                   <?php STS()->get_template( 'reports/unsatisfied-link.php',
	                                   array(
		                                   'nb_ticket_unsatisfied' => $nb_ticket_unsatisfied,
		                                   'form_date'             => '',
		                                   'to_date'               => '',
		                                   'supporter'             => ''
	                                   ) ); ?>
                                </span>
                            <span class="report__label-item" id="sts-report-legend-visited">
                                   <?php STS()->get_template( 'reports/visited-link.php',
	                                   array(
		                                   'nb_ticket_visited_not_rating' => $nb_ticket_visited_not_rating,
		                                   'form_date'                    => '',
		                                   'to_date'                      => '',
		                                   'supporter'                    => '',
	                                   ) ); ?>
                                </span>
                            <span class="report__label-item" id="sts-report-legend-other">
                                   <?php STS()->get_template( 'reports/other-link.php',
	                                   array(
		                                   'nb_ticket_other' => $nb_ticket_other,
		                                   'form_date'       => '',
		                                   'to_date'         => '',
		                                   'supporter'       => '',
	                                   ) ); ?>
                                </span>
                        </div>
					<?php
					endif;
					?>
                </div>
            </div>
            <div class="sts-block report-ticket close">
                <h3 class="sts-block__title"><?php esc_html_e( 'Listing ticket', 'sts' ) ?></h3>
                <div class="sts-block__container">
                    <div class="listing-ticket__initing">
                        <div class="listing-ticket__items">
                        </div>
                    </div>
                </div>
                <div class="sts-report-paginator"></div>
            </div>
        </div>
    </div>
</div>
