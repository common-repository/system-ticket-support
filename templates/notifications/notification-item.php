<?php
foreach ($notifications as $notification):
    ?>
    <a href="<?php echo esc_url($notification['link']) ?>"
       class="notification__item">
        <div class="notification__icon">
            <span class="dashicons dashicons-admin-comments"></span>
        </div>
        <div class="notification__info">
            <div class="notification__message">
                <?php echo wp_kses_post($notification['content']) ?>
            </div>
            <div
                    class="notification__meta">
                <?php echo esc_html(($notification['created_date'])); ?></div>
        </div>
    </a>
<?php endforeach;