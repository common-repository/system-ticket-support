<div class="message-action">
    <div class="form__message" id="sts-message-change-message-<?php echo esc_attr($message_id) ?>"></div>
    <div class="message-action__form close"
         id="message-action__form-<?php echo esc_attr($message_id) ?>">
        <form method="post" action=""
              data-sts-form-action="sts_process_edit_message" data-sts-ladda="true"
              data-sts-callback="STS.showClosedContent" id="message-action__form-<?php echo esc_attr($message_id) ?>">
            <?php wp_nonce_field('sts_edit_message_security', 'nonce') ?>
            <input type="hidden" name="message_id"
                   value="<?php echo esc_attr($message_id) ?>">
            <input type="hidden" name="ticket_id"
                   value="<?php echo esc_attr($ticket_id) ?>">
            <div
                    class="form-control-group form-control-editor">
                <textarea name="message" id="editMessage<?php echo esc_attr($message_id) ?>"
                          class="sts-text-editor form-control"></textarea>
            </div>
            <div class="button-group">
                <div class="button-group__item">
                    <button type="submit"
                            class="btn btn-primary save-message">
                        <?php esc_html_e('Save message', 'sts') ?>
                    </button>
                </div>
                <div class="button-group__item">
                    <button type="button" class="btn btn-default button-cancel-edit"
                    ><?php esc_html_e('Cancel', 'sts') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
