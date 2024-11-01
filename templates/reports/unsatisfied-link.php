<span class="report__legend unsatisfied"></span>
<a href="#"
   data-sts-ladda="true"
   data-sts-action="sts_report_filter"
   data-sts-action-param="<?php echo esc_attr(json_encode(array(
       'fromDate' => $form_date,
       'toDate'=>$to_date,
       'rating'=>0,
       'supporter'=>$supporter,
       'is_click'=>1,
       'link_unsatisfied' => 1,
       'nonce' => wp_create_nonce('sts_report_filter_security')
   ))) ?>"
   data-sts-callback="STS.showClosedContent"
>
    <span id="sts-report-legend-unsatisfied"><?php echo esc_html($nb_ticket_unsatisfied) ?></span>
    <?php
    esc_html_e('tickets unsatisfied.', 'sts');
    ?></a>
<form method="post"  data-sts-form-action="sts_report_filter" id="sts-report-paginator-unsatisfied" data-sts-callback="STS.paginatorProcess">
    <input type="hidden" name="fromDate" value="<?php echo esc_attr($form_date)?>">
    <input type="hidden" name="toDate" value="<?php echo esc_attr($to_date)?>">
    <input type="hidden" name="rating" value="<?php esc_attr_e('0','sts') ?>">
    <input type="hidden" name="is_click" value="<?php esc_attr_e('1','sts') ?>">
    <input type="hidden" name="supporter" value="<?php echo esc_attr($supporter)?>">
    <?php wp_nonce_field('sts_report_filter_security', 'nonce'); ?>
    <input type="hidden" name="current_page" id="current-page" value="<?php esc_attr_e('1','sts') ?>"
           class="current-page">
</form>