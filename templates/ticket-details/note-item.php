<?php $current_user_id = get_current_user_id(); ?>
<div class="note__item" id="note-<?php echo esc_attr($note['note_id']) ?>">
    <div class="note__container">
        <div class="form__message" id="sts-message-note"></div>
        <div class="note__content">
            <div class="note__message"><?php echo wp_kses_post($note['message']); ?></div>
        </div>
        <div class="note__meta">
           <span class="note__meta-item note__meta-item--date">
                <span class="dashicons dashicons-calendar-alt"></span><?php echo esc_html(date('F d,Y', strtotime($note['created_date']))); ?>
           </span>
            <span class="note__meta-item note__meta-item--author">
                <span class="dashicons dashicons-businessperson"></span>
                <?php echo esc_html($note['name']); ?>
            </span>
            <?php if ($current_user_id == $note['user_id']): ?>
                <a href="#" class="note__action delete-note"
                   data-sts-ladda="true"
                   data-sts-action="sts_delete_note"
                   data-sts-action-param="<?php echo esc_attr(json_encode(array(
                       'id' => $note['note_id'],
                       'nonce' => $nonce
                   ))) ?>"
                   data-sts-confirm="<?php esc_attr_e('Are you sue delete this note?','sts')?>"
                   title="<?php esc_attr_e('Delete', 'sts') ?>">
                    <span class="dashicons dashicons-trash"></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>