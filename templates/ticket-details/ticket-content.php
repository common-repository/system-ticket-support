<div class="single-ticket__content-wrapper">
    <div class="single-ticket__message" id="single-ticket__content-<?php echo esc_attr($message_id) ?>">
        <?php echo wpautop(wp_kses_post($message)); ?>
    </div>
    <?php if ($attachments): ?>
        <div class="single-ticket__attachment">
            <?php foreach ($attachments as $attachment) :
                ?>
                <a class="single-ticket__attachment-item"
                   data-source="<?php echo esc_attr(sts_attachment_url($attachment->attachment_url)) ?>"
                   title=" <?php echo esc_attr($attachment->attachment_name) ?>"
                   href="<?php echo esc_url(sts_attachment_url($attachment->attachment_url)) ?>">
                    <span class="dashicons dashicons-paperclip"></span> <?php echo esc_html($attachment->attachment_name) ?>
                </a>
            <?php
            endforeach;
            ?>
        </div>
    <?php
    endif
    ?>
</div>
