<form method="post" hidden id="form-filter-listing-message" data-sts-form-action="sts_supporter_report_show_message"
      data-sts-callback="STS.paginatorProcess">
	<input type="hidden" name="fromDate" value="<?php echo esc_attr( $from_date ) ?>">
	<input type="hidden" name="toDate" value="<?php echo esc_attr( $to_date ) ?>">
	<input type="hidden" name="supporter" value="<?php echo esc_attr( $supporter_id ) ?>">
	<input type="hidden" name="nb_message" value="<?php echo esc_attr( $nb_message ) ?>">
	<input type="hidden" name="current_page" id="current-page" value="<?php echo esc_attr( $current_page ) ?>"
	       class="current-page">
	<?php wp_nonce_field( 'sts_supporter_report_show_message_security', 'nonce' ); ?>
</form>
