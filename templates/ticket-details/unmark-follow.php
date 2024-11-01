<a class="single-ticket__meta-item unfollow"
   data-sts-ladda="true"
   data-sts-action="sts_unfollow_process"
   data-sts-action-param="<?php echo esc_attr(json_encode(array(
	   'ticket_id' => $ticket_id,
	   'nonce'     => $nonce
   ))) ?>"
   href="#">
	<span class="dashicons dashicons-admin-post"></span>
	<?php esc_html_e('Unfollow', 'sts') ?>
</a>