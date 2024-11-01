<div class="view-more-reply">
    <a href="#" class="view-more-reply-link"
       data-sts-ladda="true"
       data-sts-action="sts_load_more_message_init"
       data-sts-action-param="<?php echo esc_attr(json_encode(array(
           'ticket_id' => $ticket_id,
           'nonce' => $nonce,
           'current_page' => $current_page
       ))) ?>"
       data-sts-callback="STS.viewMoreProcess"
    ><?php esc_html_e('View more older reply', 'sts') ?>
        <i class="fal fa-sort-down"></i>
    </a>
</div>