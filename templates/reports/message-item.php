<?php foreach ( $messages as $message ): ?>
	<tr>
		<td data-mobile-label="<?php esc_attr_e( 'ID', 'sts' ) ?>" width="10%">
			<a href="<?php echo esc_url( $message['link'] ) ?>"
			   target="_blank">
				<?php echo esc_html( $message['id'] ) ?>
			</a>
		</td>
		<td data-mobile-label="<?php esc_attr_e( 'Message', 'sts' ) ?>">
			<?php echo wp_kses_post( $message['message'] ); ?>
		</td>
	</tr>
<?php endforeach; ?>