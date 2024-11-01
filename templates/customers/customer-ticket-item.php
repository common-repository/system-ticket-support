<?php foreach ($tickets as $ticket): ?>
    <div class="customer-ticket__item">
        <div class="ticket">
            <div class="ticket__avartar"><img
                    src="<?php echo esc_url($ticket['avatar']) ?>">
            </div>
            <div class="ticket__content">
                <div
                    class="ticket__author-name"><?php echo esc_html($ticket['name']); ?></div>
                <a href="<?php echo esc_url($ticket['link']) ?>"
                   class="ticket__subject"><?php echo esc_html($ticket['subject']) ?></a>

                <div class="ticket__meta">
                    <?php if ($ticket['rate'] != '' && !is_null($ticket['rate'])):
                        $rate = (int)$ticket['rate'];
                        ?>

                        <div class="ticket__meta-item ticket__rating">
                            <?php for ($i = 0; $i < $rate; $i++):
                                ?>
                                <span class="fa fa-star checked"></span>
                            <?php endfor; ?>
                            <?php for ($i = 0; $i < 5 - $rate; $i++): ?>
                                <span class="fa fa-star"></span>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    <div
                        class="ticket__meta-item ticket__created-time">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <span><?php esc_html_e('Updated ', 'sts') ?><?php echo esc_html($ticket['updating_date'])  ?></span>
                    </div>
                    <?php if ($ticket['numberMessage'] > 0): ?>
                        <div
                            class="ticket__meta-item ticket__number-comment">
                            <span class="dashicons dashicons-admin-comments"></span>
                            <span><?php echo esc_html($ticket['numberMessage']); ?></span>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>