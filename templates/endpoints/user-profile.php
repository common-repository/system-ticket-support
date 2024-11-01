<?php
global $wp_query;
$current_user = wp_get_current_user();
if ( isset( $_GET['profile_id'] ) && $_GET['profile_id'] != '' ) {
	$user_id = sanitize_text_field( $_GET['profile_id'] );
} elseif ( isset( $_GET['page'] ) && $_GET['page'] == 'my-profile' ) {
	$user_id = $current_user->ID;
}
$user_meta         = get_userdata( $user_id );
$user_current_meta = get_userdata( $current_user->ID );
$user_roles        = $user_current_meta->roles;
$user              = get_user_by( 'ID', $user_id );
?>
<div class="sts-page-main update-profile">
    <div class="sts-page-banner user-banner">
        <div class="container-fluid">
            <div class="sts-user">
				<?php STS()->get_template( 'users/user-banner.php', array(
					'avatar_url' => sts_get_avatar( $user->ID, 40, 56 ),
					'name'       => $user->display_name,
					'email'      => $user->user_email
				) ) ?>
            </div>
            <div class="form__message"></div>
        </div>
    </div>
    <div class="sts-page-container">
        <div class="container-fluid">
            <div class="sts-page-content-main">
                <div class="sts-page-profile-header">
                    <span class="sts-page-profile-header-item"><?php esc_html_e( 'Profile information', 'sts' ) ?></span>

                </div>
                <div class="update-profile-form">
                    <form method="post" action="" enctype="multipart/form-data"
                          class="form-update-profile" id="form-update-user-profile"
                          data-sts-form-action="sts_update_user_profile" data-sts-ladda="true">
						<?php wp_nonce_field( 'processing-update-profile', 'sts_update_profile_nonce_field' ); ?>
                        <input type="hidden" name="id" value="<?php echo esc_attr( $user->ID ); ?>">
						<?php if ( isset( $_GET['page'] ) ): ?>
                            <input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>">
						<?php endif; ?>
                        <div class="form-control-group">
                            <label for="username" class="form-control-label">
								<?php esc_html_e( 'Username', 'sts' ) ?>
                            </label>
                            <input class="form-control" id="username" name="username"
                                   value="<?php echo esc_attr( $user->user_login ) ?>" type="text" readonly>

                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-businessperson"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-control-group">
                                <label for="firstName" class="form-control-label">
									<?php esc_html_e( 'First name*', 'sts' ) ?>
                                </label>
                                <input class="form-control" name="firstName" id="firstName" type="text"
                                       value="<?php echo esc_attr( $user_meta->first_name ) ?>" required>
                                <div class="form-control-group-icon">
                                    <span class="dashicons dashicons-businessperson"></span>
                                </div>
                            </div>
                            <div class="form-control-group">
                                <label for="lastName" class="form-control-label">
									<?php esc_html_e( 'Last name*', 'sts' ) ?>
                                </label>
                                <input class="form-control" id="lastName" name="lastName" type="text"
                                       required value="<?php echo esc_attr( $user_meta->last_name ) ?>">
                                <div class="form-control-group-icon">
                                    <span class="dashicons dashicons-businessperson"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-control-group">
                            <label for="email" class="form-control-label">
								<?php esc_html_e( 'Email address', 'sts' ) ?>
                            </label>
                            <input class="form-control" id="email" type="email" required readonly
                                   value="<?php echo esc_attr( $user->user_email ); ?>">
                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-email"></span>
                            </div>
                            <span class="form-control-notice"><?php esc_html_e( 'If you want to change the email, please visit this', 'sts' ) ?>
                                <a href="<?php echo esc_url( get_edit_user_link( $user_id ) ) ?>"><?php esc_html_e( 'link', 'sts' ) ?></a>
                                </span>
                        </div>
                        <div class="form-control-group">
                            <label for="password" class="form-control-label">
								<?php esc_html_e( 'Password', 'sts' ) ?>
                            </label>
                            <input class="form-control" id="password" name="password" type="password">

                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-admin-network"></span>
                            </div>
                        </div>
                        <div class="form-control-group">
                            <label for="rppassword" class="form-control-label">
								<?php esc_html_e( 'Repeat password', 'sts' ) ?>
                            </label>
                            <input id="rppassword" class="form-control" name="rppassword" type="password">
                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-admin-network"></span>
                            </div>
                        </div>
						<?php
						if ( $current_user->ID == $user_id && in_array( 'subscriber', $user_roles ) ):
							$is_unsubscribe = get_user_meta( $user_id, 'sts_is_receive_mail', true );
							?>
                            <div class="form-control-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="unsubscribe" <?php if ( $is_unsubscribe == 0 ) {
											echo 'checked';
										} ?>>
										<?php esc_html_e( "Unsubscribe email", "sts" ) ?>
                                    </label>
                                </div>
								<?php if ( $is_unsubscribe == 0 ): ?>
                                <span class="form-control-notice"><?php esc_html_e( 'You have unsubscribed our email. If you want to receive our email, please uncheck this field!', 'sts' ) ?>
									<?php endif;; ?>
                            </div>
						<?php endif; ?>
						<?php if ( ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles )
						             || in_array( 'administrator', $user_roles ) ) && $current_user->ID == $user_id ):
							$user_signature = get_user_meta( $user_id, 'sts_user_signature', true );
							?>
                            <div class="form__item form-control-editor">
                                <label class="form__label" for="editor">
									<?php esc_html_e( 'Your signature:', 'sts' ) ?>
                                </label>
                                <textarea name="userSignature" id="user-signature"
                                          class="sts-text-editor form-control"><?php echo wpautop( wp_kses_post( $user_signature ) ); ?></textarea>
                            </div>
							<?php $is_on_signature = get_user_meta( $user_id, 'sts_is_on_signature', true );
							?>
                            <div class="form-control-group">
                                <div class="checkbox">
									<?php if ( $is_on_signature == 0 ): ?>
                                        <label>
                                            <input type="checkbox" name="onSignature">
											<?php esc_html_e( "On signature", "sts" ) ?>
                                        </label>
									<?php else: ?>
                                        <label>
                                            <input type="checkbox" name="offSignature">
											<?php esc_html_e( "Off signature", "sts" ) ?>
                                        </label>
									<?php endif; ?>

                                </div>
                            </div>
						<?php endif; ?>
                        <div class="form-control-group form-control-file">
                            <label for="avatar" class="label">
								<?php esc_html_e( 'Avatar image:', 'sts' ) ?>
                            </label>
                            <input id="avatar" name="avatar" type="file">
                        </div>
                        <div class="form-button-group">
                            <div class="form-button-group-item">
                                <button class="btn btn-primary" type="submit">
									<?php esc_html_e( 'Update profile', 'sts' ) ?>
                                </button>
                            </div>
                            <div class="form-button-group-item">
                                <a href="<?php echo esc_url( wp_get_referer() ); ?>" class="btn btn-default">
									<?php esc_html_e( 'Come back', 'sts' ) ?>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>