<?php
foreach ( $contents as $content ):
	?>
	<tr>
		<td class="sts-number-order"
		    data-mobile-label="<?php esc_attr_e( 'Supporter', 'sts' ) ?>">
			<?php echo esc_html( $content['supporter_name'] ) ?>
		</td>
		<td data-mobile-label="<?php esc_attr_e( 'From date', 'sts' ) ?>">
			<?php echo esc_html( date( 'd-m-Y', strtotime( $content['from_date'] ) ) ) ?>
		</td>
		<td data-mobile-label="<?php esc_attr_e( 'To date', 'sts' ) ?>">
			<?php echo esc_html( date( 'd-m-Y', strtotime( $content['to_date'] ) ) ) ?>
		</td>
		<td data-mobile-label="<?php esc_attr_e( 'Number of message', 'sts' ) ?>">
			<a href="#"
			   data-sts-ladda="true"
			   data-sts-action="sts_supporter_report_show_message"
			   data-sts-action-param="<?php echo esc_attr( json_encode( array(
				   'fromDate'     => $content['from_date'],
				   'toDate'       => $content['to_date'],
				   'supporter'    => $content['supporter_id'],
				   'nb_message'   => $content['nb_message'],
				   'current_page' => $current_page,
				   'nonce'        => wp_create_nonce( 'sts_supporter_report_show_message_security' )
			   ) ) ) ?>"
			   data-sts-callback="STS.showClosedContent">
				<?php echo esc_html( $content['nb_message'] ) ?>
			</a>
		</td>
		<td data-mobile-label="<?php esc_attr_e( 'Number of ticket', 'sts' ) ?>">
			<?php echo esc_html( $content['nb_ticket'] ) ?>
		</td>
	</tr>
<?php endforeach;
