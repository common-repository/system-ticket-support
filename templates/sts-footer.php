<div class="sts-footer py-4">
    <div class="container">
        <div class="d-flex sts-footer-container flex-wrap flex-md-nowrap justify-content-center justify-content-md-start align-items-center">
			<?php
			$menus = get_option( 'sts_menu_footer' );
			if ( $menus != false ):
				$new_menu = unserialize( $menus );
				?>
                <div class="sts-footer-menu">
                    <ul class="list-unstyled list-inline mb-3 mb-md-0">
						<?php
						foreach ( $new_menu as $menu ):
							?>
                            <li class="list-inline-item"><a href="<?php echo esc_url( $menu['menu_link'] ) ?>"
                                                            target="_blank"><?php echo esc_html( $menu['menu_title'] ) ?></a>
                            </li>
						<?php
						endforeach;
						?>
                    </ul>
                </div>
			<?php
			endif;
			?>
            <div class="sts-footer-copyright ml-0 ml-md-auto">
				<?php esc_html_e( 'Copyright &copy; G5Plus Inc. All Rights Reserved', 'sts' ) ?>
            </div>
        </div>
    </div>
</div>
