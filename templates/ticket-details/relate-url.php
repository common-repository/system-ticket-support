<div class="ticket-metadata__item">
    <span class="ticket-metadata__label">
        <?php esc_html_e( 'Website url', 'sts' ) ?>
    </span>
    <div class="ticket-metadata__content">
        <i class="fal fa-external-link"></i>
        <a href="<?php echo esc_url( $website_url ) ?>" target="_blank">
			<?php echo esc_html( $website_url ); ?>
        </a>
    </div>
</div>
