<a class="single-ticket__meta-item process"
   data-sts-ladda="true"
   data-sts-action="sts_mark_process"
   data-sts-action-param="<?php echo esc_attr(json_encode(array(
       'ticket_id' => $ticket_id,
       'nonce' => $nonce,
   ))) ?>"
   data-sts-callback="STS.asssignSelf"
   href="#"><span class="dashicons dashicons-flag"></span><?php esc_html_e('Mark process', 'sts') ?>
</a>