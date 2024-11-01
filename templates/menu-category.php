<li class="main-menu__item <?php echo esc_attr($is_endpoint_category ? 'active' : '')  ?>">
    <a href="<?php echo esc_url( sts_support_page_url( 'categories' ) ); ?>">
        <span class="main-menu__icon"><span class="dashicons dashicons-category"></span></span>
        <span class="main-menu__name"><?php esc_html_e( 'Categories', 'sts' ) ?></span>
    </a>
</li>