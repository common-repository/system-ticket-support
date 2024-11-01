<span class="report__legend other"></span>
<a href="#"
   data-sts-ladda="true"
   data-sts-action="sts_report_filter"
   data-sts-action-param="<?php echo esc_attr(json_encode(array(
       'fromDate' => $form_date,
       'toDate' => $to_date,
       'rating' => '',
       'supporter' => $supporter,
       'is_click' => 1,
       'link_other' => 1,
       'nonce' => wp_create_nonce('sts_report_filter_security')
   ))) ?>"
   data-sts-callback="STS.showClosedContent">
    <span id="sts-report-legend-other"><?php echo esc_html($nb_ticket_other) ?></span>
    <?php
    esc_html_e(' other tickets.', 'sts');
    ?></a>
<form method="post" data-sts-form-action="sts_report_filter"
      id="sts-report-paginator-other" data-sts-callback="STS.paginatorProcess">
    <input type="hidden" name="fromDate" value="<?php echo esc_attr($form_date) ?>">
    <input type="hidden" name="toDate" value="<?php echo esc_attr($to_date) ?>">
    <input type="hidden" name="rating" value="">
    <input type="hidden" name="is_click" value="<?php esc_attr_e('1','sts') ?>">
    <input type="hidden" name="link_other" value="<?php esc_attr_e('1','sts') ?>">
    <input type="hidden" name="supporter" value="<?php echo esc_attr($supporter) ?>">
    <input type="hidden" name="current_page" id="current-page" value="<?php esc_attr_e('1','sts') ?>"
           class="current-page">
    <?php wp_nonce_field('sts_report_filter_security', 'nonce'); ?>
</form>