<div class="top-bar no-login">
    <div class="container">
        <div class="top-bar__container">
            <div class="top-bar__logo">
	            <?php the_custom_logo() ?>
            </div>

            <div class="top-bar__right">
                <ul class="top-bar__right-menu">
                    <li class="top-bar__right-menu-item top-bar__right-menu-item--ticket">
                        <a href="<?php echo esc_url(sts_support_page_url('submit-ticket')) ?>"
                           class="top-bar__right-menu-link"
                           title="<?php esc_attr_e('Submit ticket', 'sts') ?>"><span class="dashicons dashicons-tag"></span><span
                                    class="top-bar__right-menu-label"><?php esc_html_e("Submit a ticket", "sts") ?></span></a>
                    </li>
                    <li class="top-bar__right-menu-item">
                        <a href="<?php echo esc_url((sts_support_page_url('login'))) ?>"
                           class="btn btn-primary"><?php esc_html_e("Sign in", "sts") ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>