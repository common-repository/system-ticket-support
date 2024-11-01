<div style="background:#f3f3f5;">
    <div style="max-width: 600px;margin: 0 auto;padding-top: 30px;">
        <div style="padding: 20px;text-align: center;background: #039be5;border-radius: 4px 4px 0 0;line-height: 0;">
            <img src="<?php echo esc_url( STS()->plugin_url( 'assets/images/logo-white.png' ) ) ?>"
                 style="width: 80px;">
        </div>
        <div style="padding:30px;background: white;border-radius: 0 0 4px 4px;">
        <span style="font-size: 18px; display:block;margin-bottom: 30px">
            <?php printf( esc_html__( 'Hi %s,', 'sts' ), $customer_name ); ?>
        </span>
            <div style="margin-bottom: 50px;">
                <p style="margin-bottom: 30px;">
					<?php esc_html_e( 'Please visit this link to verify your account.', 'sts' ) ?>
                </p>
                <br>
                <div style="margin-bottom: 30px;">
                    <span style="padding:7px 14px;background:#039be5;border-radius:4px;"><?php echo wp_kses_post( $profile_url ) ?></span>
                </div>

                <p><?php esc_html_e( 'Thank you for using our service!', 'sts' ) ?></p>
                <p><?php esc_html_e( 'Best regards!', 'sts' ) ?></p>
                <p><?php esc_html_e( 'G5Plus company', 'sts' ) ?></p>
            </div>
        </div>
        <div style="background:#039be5; width: 100%;border-radius:0 0 5px 5px; text-align: center">
            <a style="display:inline-block;padding: 10px 5px; text-decoration: none;line-height: 0;"
               href="https://www.facebook.com/G5Theme/"
               title="<?php esc_attr_e( 'Facebook', 'sts' ) ?>"><img alt="<?php esc_attr_e( 'facebook', 'sts' ) ?>"
                                                                     src="<?php echo esc_url( STS()->plugin_url( 'assets/images/facebook.png' ) ) ?>"
                                                                     style="width: 25px;">
            </a><a href="https://twitter.com/g5plusnet" title="<?php esc_attr_e( 'Twitter', 'sts' ) ?>"
                   style="display:inline-block;padding: 10px 5px; text-decoration: none;line-height: 0;"><img
                        alt="<?php esc_attr_e( 'twitter', 'sts' ) ?>"
                        src="<?php echo esc_url( STS()->plugin_url( 'assets/images/twitter.png' ) ) ?>"
                        style="width: 25px;">
            </a><a href="https://vn.linkedin.com/in/g5plus" title="<?php esc_attr_e( 'LinkIn', 'sts' ) ?>"
                   style="display:inline-block;padding: 10px 5px; text-decoration: none;line-height: 0;"><img
                        alt="<?php esc_attr_e( 'linkein', 'sts' ) ?>"
                        src="<?php echo esc_url( STS()->plugin_url( 'assets/images/linkedin.png' ) ) ?>"
                        style="width: 25px;">
            </a><a href="https://www.youtube.com/c/g5plus/" title="<?php esc_attr_e( 'Youtube', 'sts' ) ?>"
                   style="display:inline-block;padding: 10px 5px; text-decoration: none;line-height: 0;"><img
                        alt="<?php esc_attr_e( 'youtube', 'sts' ) ?>"
                        src="<?php echo esc_url( STS()->plugin_url( 'assets/images/youtube.png' ) ) ?>"
                        style="width: 25px;">
            </a>
        </div>
    </div>
    <div style="font-size: 11px;text-align: center;color: #5b7e8e;padding: 20px 0;">
		<?php printf( esc_html__( 'This mail is sent auto. Please do not reply to this email! If you do not want to receive our mail, please click %s', 'sts' ), $unsubscribe_url ) ?>
    </div>
</div>