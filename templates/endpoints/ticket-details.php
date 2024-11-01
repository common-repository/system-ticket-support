<?php
$current_user                    = wp_get_current_user();
$user_meta                       = get_userdata( $current_user->ID );
$user_roles                      = $user_meta->roles;
if ( isset( $_GET['t'] ) && $_GET['t'] != '' ):
	$ticket_id = sanitize_text_field( $_GET['t'] );
	$ticket                      = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
	if ( $ticket ):
		if ( ( ( in_array( 'subscriber', $user_roles ) && $current_user->ID == $ticket->customer_id ) ||
		       in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles )
		       || in_array( 'administrator', $user_roles ) ) ):

			$allMessage = STS()->db()->messages()->get_all_message_answer( $ticket_id );
			$question            = STS()->db()->messages()->get_question( $ticket_id );
			$attachmentQuestions = STS()->db()->attachments()->get_attachment_by_message( $question->id );
			$customer            = get_user_by( 'ID', $ticket->customer_id );
			if ( $customer ) :
				$supporters = get_users( array( 'role__in' => array( 'supporter', 'leader_supporter' ) ) );
				$limit_message   = STS()->db()->messages()->limit_message;
				$supporter_name  = 'Not assign';
				if ( ! is_null( $ticket->supporter_id ) && $ticket->supporter_id != 0 && $ticket->supporter_id != '' ) {
					$supporter      = get_user_by( 'ID', $ticket->supporter_id );
					$supporter_name = $supporter->display_name;
				}
				?>
				<?php if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles )
				           || in_array( 'administrator', $user_roles ) ): ?>
                <div class="supporter-processing-anchor">
					<?php if ( $ticket->is_lock == 1 ) {
						$user_lock = get_user_by( 'ID', $ticket->user_lock );
						STS()->get_template( 'tickets/supporter-processing.php', array( 'supporter_name' => $user_lock->display_name ) );
					} ?>
                </div>
			<?php endif; ?>
                <div class="sts-page-main">
                    <div class="sts-page-banner">
                        <div class="container-fluid">
                            <div class="sts-page-banner-content banner-center">
                                <h3 class="sts-page-title">
									<?php
									esc_html_e( 'Ticket details', 'sts' )
									?>
                                </h3>
                            </div>
                            <div class="form__message" id="sts-message-details"></div>
                        </div>
                    </div>
                    <div class="sts-page-container">
                        <div class="page-2column">
                            <div class="container-fluid">
                                <div class="page-2column__wrapper">
                                    <div class="page-2column__main">
                                        <div class="sts-block single-ticket">
                                            <div class="sts-page-profile-header ">
                                                <h4 class="sts-page-profile-header-item"><?php echo esc_html( $ticket->subject ); ?>
                                                </h4>
												<?php if ( ! is_null( $ticket->rate ) ): ?>
                                                    <span class="ticket-rating">
													<?php if ( $ticket->rate == 1 ): ?>
                                                        <span class="dashicons dashicons-thumbs-up"></span>
													<?php elseif ( $ticket->rate == 0 ): ?>
                                                        <span class="dashicons dashicons-thumbs-up"></span>
													<?php endif; ?>
												</span>
												<?php endif; ?>
                                            </div>

                                            <div class="single-ticket__container ">

                                                <div class="single-ticket__wrapper">
													<?php STS()->get_template( 'ticket-details/ticket-avatar.php',
														array(
															'avatar_url'  => sts_get_avatar( $customer->ID, 100, 100 ),
															'profile_url' => sts_support_page_url( 'customer-details/?customer_id=' . $ticket->customer_id )
														) ) ?>
                                                    <div class="single-ticket__content">
														<?php STS()->get_template( 'ticket-details/ticket-author-info.php',
															array(
																'avatar_url'   => sts_get_avatar( $customer->ID, 100, 100 ),
																'profile_url'  => sts_support_page_url( 'customer-details/?customer_id=' . $ticket->customer_id ),
																'name'         => $customer->display_name,
																'created_date' => $ticket->created_date,
																'is_question'  => 1,
															) ) ?>
                                                        <div class="single-ticket__content-wrapper">
                                                            <div class="single-ticket__message <?php if ( $question->message == '' ) {
																echo 'no-message';
															} ?>"
                                                                 id="single-ticket__content-<?php echo esc_attr( $question->id ) ?>">
																<?php echo wpautop( wp_kses_post( $question->message ) ); ?>
                                                            </div>
															<?php if ( $attachmentQuestions ): ?>
                                                                <div class="single-ticket__attachment <?php if ( $question->message == '' ) {
																	echo 'no-message';
																} ?>">
																	<?php
																	foreach ( $attachmentQuestions as $attachmentQuestion ):
																		?>
                                                                        <a class="single-ticket__attachment-item"
                                                                           data-source="<?php echo esc_attr( sts_attachment_url( $attachmentQuestion->attachment_url ) ) ?>"
                                                                           title=" <?php echo esc_attr( $attachmentQuestion->attachment_name ) ?>"
                                                                           href="<?php echo esc_url( sts_attachment_url( $attachmentQuestion->attachment_url ) ) ?>">
                                                                            <span class="dashicons dashicons-paperclip"></span> <?php echo esc_html( $attachmentQuestion->attachment_name ) ?>
                                                                        </a>
																	<?php
																	endforeach; ?>
                                                                </div>
															<?php
															endif;
															?>
                                                        </div>
                                                        <div class="single-ticket__meta">
															<?php if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ): ?>
                                                                <div class="single-ticket__meta-follow">
																	<?php
																	$user_following = STS()->db()->tickets()->get_user_follow_ticket( $ticket_id );
																	if ( ! in_array( $current_user->ID, $user_following ) ):
																		?>
																		<?php STS()->get_template( 'ticket-details/mark-follow.php',
																		array(
																			'ticket_id' => $ticket_id,
																			'nonce'     => wp_create_nonce( 'sts_mark_follow_security' )
																		) ) ?>
																	<?php else: ?>
																		<?php STS()->get_template( 'ticket-details/unmark-follow.php',
																			array(
																				'ticket_id' => $ticket_id,
																				'nonce'     => wp_create_nonce( 'sts_unfollow_security' )
																			) ) ?>

																	<?php endif ?>
                                                                </div>
																<?php if ( $ticket->status != 3 ): ?>
                                                                    <div class="single-ticket__meta-process"
                                                                         data-ticket="<?php echo esc_attr( $ticket_id ) ?>"
                                                                         data-nonce="<?php echo esc_attr( wp_create_nonce( 'sts_ticket_details_auto_send_security' ) ) ?>">
																		<?php if ( ( $ticket->is_lock == 0 || is_null( $ticket->is_lock ) ) ): ?>

																			<?php STS()->get_template( 'ticket-details/mark-process.php',
																				array(
																					'ticket_id' => $ticket_id,
																					'nonce'     => wp_create_nonce( 'sts_mark_process_security' )
																				) ) ?>
																		<?php elseif ( $ticket->is_lock == 1 && $ticket->user_lock == $current_user->ID ): ?>

																			<?php STS()->get_template( 'ticket-details/unmark-process.php',
																				array(
																					'ticket_id' => $ticket_id,
																					'nonce'     => wp_create_nonce( 'sts_unmark_process_security' )
																				) ) ?>
																		<?php else:
																			$user_lock = get_user_by( 'ID', $ticket->user_lock );
																			STS()->get_template( 'tickets/supporter-marking-process.php',
																				array(
																					'supporter_name' => $user_lock->display_name,
																				) );
																		endif; ?>
                                                                    </div>
																<?php endif ?>
															<?php endif; ?>

															<?php
															if ( ( ! $allMessage && $current_user->ID == $ticket->customer_id ) || in_array( 'administrator', $user_roles ) ) {
																STS()->get_template( 'ticket-details/message-action-button.php',
																	array(
																		'is_question' => 1,
																		'ticket_id'   => $ticket_id,
																		'message_id'  => $question->id,
																		'nonce'       => wp_create_nonce( 'sts_frontend_delete_message' ),
																		'nonce_edit'  => wp_create_nonce( 'sts_get_message_security' )
																	)
																);
															}
															?>
                                                        </div>
                                                    </div>

                                                </div>
												<?php
												if ( ( ! $allMessage && $current_user->ID == $ticket->customer_id ) || in_array( 'administrator', $user_roles ) ): ?>
													<?php STS()->get_template( 'ticket-details/message-action.php',
														array(
															'ticket_id'  => $ticket_id,
															'message_id' => $question->id
														)
													)
													?>
												<?php endif ?>
                                            </div>
                                            <div class="single-ticket__less-message">
												<?php
												$contents = array();
												foreach ( $allMessage as $message ):
													$user  = get_user_by( 'ID', $message->user_id );
													$meta  = get_userdata( $user->ID );
													$roles = $meta->roles;
													if ( in_array( 'supporter', $roles ) || in_array( 'leader_supporter', $roles ) || in_array( 'administrator', $roles ) ) {
														$profile_url = sts_support_page_url( 'supporter-details/?supporter_id=' . $message->user_id );
													} elseif ( in_array( 'subscriber', $roles ) ) {
														$profile_url = sts_support_page_url( 'customer-details/?customer_id=' . $message->user_id );
													}
													$attachments   = STS()->db()->attachments()->get_attachment_by_message( $message->id );
													$message_ids[] = $message->id;
													$contents[]    = array(
														'id'           => $message->id,
														'message'      => $message->message,
														'attachments'  => $attachments,
														'avatar'       => sts_get_avatar( $user->ID, 100, 100 ),
														'name'         => $user->display_name,
														'created_date' => $message->created_date,
														'roles'        => $roles,
														'admin'        => in_array( 'administrator', $user_roles ),
														'current_user' => $current_user->ID == $message->user_id,
														'ticket_id'    => $ticket_id,
														'profile_url'  => $profile_url,
														'is_question'  => 0,
														'message_url'  => sts_link_ticket( $ticket_id ) . '#message-' . $message->id

													);
												endforeach;
												STS()->get_template( 'ticket-details/message-item.php', array(
													'contents'     => $contents,
													'nonce_edit'   => wp_create_nonce( 'sts_get_message_security' ),
													'nonce_delete' => wp_create_nonce( 'sts_frontend_delete_message' )
												) );
												?>
                                            </div>
											<?php if ( ! is_null( $ticket->close_date ) && $ticket->status == 3 ): ?>
                                                <div class="single-ticket__close-date">
													<?php printf( esc_html__( 'Closed at %s', 'sts' ), sts_time_ago( strtotime( $ticket->close_date ) ) ) ?>
                                                </div>
											<?php endif; ?>
                                            <div class="single-ticket__action
                                    <?php if ( (
												                                                           ( ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) ) && ( $ticket->is_lock == 0 || is_null( $ticket->is_lock ) ) )
												                                                           || $current_user->ID == $ticket->customer_id
												                                                           || $ticket->user_lock == $current_user->ID
												                                                           || in_array( 'administrator', $user_roles ) ) && $ticket->status != 3 ) {
												echo '';
											} else {
												echo 'close';
											}
											?>" id="sts-action-reply">
                                                <a class="btn btn-primary button-reply" id="post-reply" href="#"
                                                   data-sts-ladda="true"
                                                   data-sts-action="sts_process_get_form_reply"
                                                   data-sts-action-param="<?php echo esc_attr( json_encode( array(
													   'ticket_id' => $ticket_id,
													   'nonce'     => wp_create_nonce( 'sts_process_get_form_reply_security' )
												   ) ) ) ?>"
                                                   data-sts-callback="STS.settingEditor">
                                                    <span class="dashicons dashicons-edit"></span>
													<?php esc_html_e( 'Post a reply', 'sts' ) ?>
                                                </a>
												<?php if ( ( $ticket->is_lock == 1 && $ticket->user_lock == $current_user->ID ) || ( in_array( 'administrator', $user_roles ) && $ticket->is_lock == 1 ) ): ?>
                                                    <a href="#" class="btn btn-primary unlock-reply"
                                                       data-sts-ladda="true"
                                                       data-sts-action="sts_process_cancel_form_reply"
                                                       data-sts-action-param="<?php echo esc_attr( json_encode( array(
														   'ticket_id' => $ticket_id,
														   'nonce'     => wp_create_nonce( 'sts_process_cancel_form_reply_security' )
													   ) ) ) ?>"
                                                       data-sts-callback="STS.showClosedContent"
                                                       id="unlock-reply"><?php esc_html_e( 'Unlock reply', 'sts' ) ?></a>
												<?php endif; ?>
                                                <form method="post" action="" id="form-reply-ticket"
                                                      data-sts-form-action="sts_reply_ticket" data-sts-ladda="true"
                                                      enctype="multipart/form-data">
                                                    <div class="form__message" id="sts-message-reply-ticket"></div>
													<?php wp_nonce_field( 'processing-ticket-details', 'sts_ticket_detail_nonce_field' ); ?>
                                                    <div class="single-ticket__action-content close">
														<?php if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ):
															?>
                                                            <div class="form-control-group">
                                                                <select class="form-control template"
                                                                        id="sts-rely-template"
                                                                        data-sts-change-action="sts_get_template"
                                                                        data-sts-action-param="<?php echo esc_attr( json_encode( array(
																	        'nonce' => wp_create_nonce( 'sts_get_template_security' )
																        ) ) ) ?>"
                                                                        data-sts-callback="STS.settingEditor">
                                                                    <option value="0" selected>
																		<?php esc_html_e( 'Choose template', 'sts' ) ?>
                                                                    </option>
																	<?php $templates    = STS()->db()->templates()->getting_template_by_user( $current_user->ID );
																	if ( $templates ):
																		foreach ( $templates as $template ):
																			$tags = STS()->db()->templates()->get_tags_by_template_id( $template->id );
																			$tags[]     = $template->template_name;
																			$search_str = implode( ",", $tags );
																			?>
                                                                            <option data-search="<?php echo esc_attr( $search_str ) ?>"
                                                                                    value="<?php echo esc_attr( $template->id ) ?>"><?php echo esc_html( $template->template_name ); ?></option>
																		<?php
																		endforeach;
																	endif; ?>
                                                                </select>
                                                            </div>
														<?php endif; ?>

                                                        <div class="form-control-group form-control-editor">
                                                            <input type="hidden" name="ticketID"
                                                                   value="<?php echo esc_attr( $ticket_id ); ?>">
                                                            <textarea name="message" id="editor"
                                                                      class="sts-text-editor"></textarea>
                                                        </div>
                                                        <div class="button-group">
                                                            <div class="button-group__item button-group__item--attachment">
                                                                <label class="form-label-attachment">
                                                                    <span class="dashicons dashicons-paperclip"></span>
																	<?php esc_html_e( "Add attachment( jpg, jpeg, png, gif allowed):", "sts" ) ?>
                                                                </label>
                                                                <input type="file" name="attachment[]" id="attachment"
                                                                       multiple>
                                                            </div>
                                                        </div>
                                                        <div class="button-group message-action">
                                                            <div class="button-group__item">
                                                                <button class="btn btn-primary" type="submit">
																	<?php esc_html_e( 'Post reply', 'sts' ) ?>
                                                                </button>
                                                            </div>
                                                            <div class="button-group__item">
                                                                <button class="btn btn-default button-cancel-reply"
                                                                        type="button"
                                                                        data-sts-ladda="true"
                                                                        data-sts-action="sts_process_cancel_form_reply"
                                                                        data-sts-action-param="<?php echo esc_attr( json_encode( array(
																	        'ticket_id' => $ticket_id,
																	        'nonce'     => wp_create_nonce( 'sts_process_cancel_form_reply_security' )
																        ) ) ) ?>"
                                                                        data-sts-callback="STS.showClosedContent">
																	<?php esc_html_e( 'Cancel', 'sts' ) ?>
                                                                </button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
										<?php if ( ! is_null( $ticket->rate_info ) ):
											$rate_info = $ticket->rate_info;
											if ( $ticket->rate_info == '' ) {
												if ( $ticket->rate == 0 ) {
													$rate_info = 'Unsatisfied';
												} else {
													$rate_info = 'Satisfied';
												}
											}
											?>
                                            <div class="sts-block sts-rate-info">
                                                <h4 class="sts-block__title"><?php esc_html_e( 'Rating info', 'sts' ) ?>
                                                    <span class="rate-date"><?php printf( esc_html__( 'Latest rate: %s', 'sts' ), sts_time_ago( strtotime( $ticket->rate_date ) ) ); ?></span>
                                                </h4>
                                                <div class="sts-block__container">
													<?php echo esc_html( $rate_info ) ?>
                                                </div>
                                            </div>
										<?php endif; ?>
										<?php
										if ( isset( $_GET['key'] ) && $_GET['key'] != '' ):
											$key = sanitize_text_field( $_GET['key'] );
											$date = date( "Y-m-d H:i:s" );
											$key_obj = STS()->db()->key_mail_rate()->get_key_by_user_ticket_code( $current_user->ID, $ticket_id, $key );
											if ( $key_obj ):
												if ( $current_user->ID == $ticket->customer_id
												     && $ticket->status == 3 && $key == md5( $key_obj->key_name )
												     && $date < date( $key_obj->date_expired ) ):
													?>
                                                    <div class="sts-block sts-form-rate close" id="form-rating">
                                                        <h3 class="sts-block__title"><?php esc_html_e( 'Your appreciation make us support better', 'sts' ) ?></h3>
                                                        <div class="form__container">
                                                            <form action="" method="post" id="form-rating"
                                                                  data-sts-form-action="sts_save_rating"
                                                                  class="form-rating"
                                                                  data-sts-ladda="true">
                                                                <div class="form__message"
                                                                     id="sts-message-rating"></div>
																<?php wp_nonce_field( 'processing-save-rating', 'sts_submit_rating_field' ); ?>
                                                                <div class="form__item">
                                                                    <label class="form__label"><?php esc_html_e( 'How would you rate the support you received?', 'sts' ) ?></label>

                                                                    <div class="rate form-group">
                                                                        <div class="form-group__item">
                                                                            <input type="radio" id="good" name="rate"
                                                                                   value="<?php esc_attr_e( '1', 'sts' ) ?>"
                                                                                   checked/>
                                                                            <label for="good" title="text">
                                                                                <span class="dashicons dashicons-thumbs-up"></span>
																				<?php esc_html_e( "Good,I'm satisfied", 'sts' ) ?>
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-group__item form-group__item--bad">
                                                                            <input type="radio" id="bad" name="rate"
                                                                                   value="<?php esc_attr_e( '0', 'sts' ) ?>"/>
                                                                            <label for="bad" title="text">
                                                                                <span class="dashicons dashicons-thumbs-up"></span>
																				<?php esc_html_e( "Bad I'm unsatisfied", 'sts' ) ?>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form__item">
                                                                    <label class="form__label" for="editor">
																		<?php esc_html_e( 'Add a comment about the quality of support you received (optional):', 'sts' ) ?>
                                                                    </label>
                                                                    <textarea id="editor"
                                                                              class="form-control form__control"
                                                                              name="rateInfo"></textarea>
                                                                </div>
                                                                <input type="hidden" name="ticketID"
                                                                       value="<?php echo esc_attr( $ticket_id ) ?>">
                                                                <input type="hidden" name="key"
                                                                       value="<?php echo esc_attr( $key ) ?>">
                                                                <div class="form-button-group">
                                                                    <div class="form-button-group-item">
                                                                        <button class="btn btn-primary" type="submit">
																			<?php esc_html_e( 'Update', 'sts' ) ?>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
												<?php
												else:
													?>
                                                    <span class="sts-warning form-rating-warning"><?php esc_html_e( 'This rating url is invalid or has expired. 
                                            Notice: url rating has expired in 7 days from you received our mail.', 'sts' ) ?></span>
												<?php
												endif;
											else:
												?>
                                                <span class="sts-warning form-rating-warning"><?php esc_html_e( 'This rating url is invalid or has expired. 
                                            Notice: url rating has expired in 7 days from you received our mail.', 'sts' ) ?></span>
											<?php
											endif;
										endif;
										?>
                                    </div>
                                    <div class="sts-sidebar">
                                        <div class="sts-sidebar__item sts-block">
                                            <div class="ticket-metadata">
                                                <div class="sts-page-profile-header">
                                                    <h4 class="sts-page-profile-header-item"><?php esc_html_e( 'Ticket details', 'sts' ) ?></h4>
                                                </div>
                                                <div class="ticket-metadata">
													<?php if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ) : ?>
                                                        <div class="ticket-metadata__submitting">
                                                            <div class="sts-sidebar__wrapper">
                                                                <div class="ticket-metadata__can-update">
                                                                    <div class="ticket-metadata__item">
                                                                        <span class="ticket-metadata__label"><?php esc_html_e( 'Status', 'sts' ) ?></span>

                                                                        <div class="ticket-metadata__content">
                                                                            <span class="dashicons dashicons-clock"></span>
                                                                            <span class="ticket-metadata__status">
                                                                    <?php switch ( $ticket->status ) {
	                                                                    case 1:
		                                                                    echo esc_html__( 'Request', 'sts' );
		                                                                    break;
	                                                                    case 2:
		                                                                    echo esc_html__( 'Responded', 'sts' );
		                                                                    break;
	                                                                    case 3:
		                                                                    echo esc_html__( 'Close', 'sts' );
		                                                                    break;
                                                                    }
                                                                    ?>
                                                                    </span>
                                                                        </div>

                                                                        <a href="#"
                                                                           class="ticket-metadata__action edit
                                                                    <?php if ( $current_user->ID != $ticket->supporter_id && ! in_array( 'administrator', $user_roles ) ) {
																			   echo 'close';
																		   } ?>" id="sts-change-status">
                                                                            <span class="dashicons dashicons-edit"></span>
                                                                        </a>
                                                                    </div>
                                                                    <div class="sts-form-close form-status close">
                                                                        <form action="" method="post"
                                                                              data-sts-form-action="sts_change_status"
                                                                              class="form form-change-status"
                                                                              data-sts-ladda="true"
                                                                              data-sts-callback="STS.changeStatus">
                                                                            <div class="form__message"
                                                                                 id="sts-message-change-status"></div>
																			<?php wp_nonce_field( 'sts_change_status_security', 'nonce' ) ?>
                                                                            <input type="hidden" name="ticket_id"
                                                                                   value="<?php echo esc_attr( $ticket_id ) ?>">
                                                                            <div class="form-control-group">
                                                                                <label class="form-control-label"
                                                                                       for="status-ticket-details">
																					<?php esc_html_e( 'Status', 'sts' ) ?>
                                                                                </label>
                                                                                <select class="form-control"
                                                                                        id="status-ticket-details"
                                                                                        name="status"
                                                                                        data-nonce="<?php echo esc_attr( wp_create_nonce( 'sts_change_status_security' ) ) ?>">
																					<?php if ( $ticket->status == 3 ): ?>
                                                                                        <option value="<?php esc_attr_e( '1', 'sts' ) ?>"><?php esc_html_e( 'Unclose', 'sts' ) ?></option>
																					<?php else: ?>
                                                                                        <option value="<?php esc_attr_e( '3', 'sts' ) ?>"><?php esc_html_e( 'Close', 'sts' ) ?></option>
																					<?php endif; ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="button-group">
                                                                                <div class="button-group__item">
                                                                                    <button type="submit"
                                                                                            class="btn btn-primary">
																						<?php esc_html_e( 'Update', 'sts' ) ?>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="button-group__item">
                                                                                    <button type="button"
                                                                                            class="btn btn-default button-cancel-update">
																						<?php esc_html_e( 'Cancel', 'sts' ) ?>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
																<?php if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ): ?>
                                                                    <div class="ticket-metadata__can-update">
                                                                        <div class="ticket-metadata__item">
                                                                            <span class="ticket-metadata__label"><?php esc_html_e( 'Assigned', 'sts' ) ?></span>

                                                                            <div class="ticket-metadata__content">
                                                                                <span class="dashicons dashicons-businessperson"></span>
                                                                                <span class="ticket-metadata__supporter"><?php echo esc_html( $supporter_name ); ?></span>
                                                                            </div>
																			<?php if ( in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ): ?>
                                                                                <a href="#"
                                                                                   class="ticket-metadata__action edit">
                                                                                    <span class="dashicons dashicons-edit"></span>
                                                                                </a>
																			<?php endif; ?>
                                                                        </div>
                                                                        <div class="sts-form-close form-supporter close">
                                                                            <form action="" method="post"
                                                                                  data-sts-form-action="sts_change_supporter"
                                                                                  data-sts-ladda="true"
                                                                                  data-sts-callback="STS.asssignSelf">
                                                                                <div class="form__message"
                                                                                     id="sts-message-change-supporter"></div>
																				<?php wp_nonce_field( 'sts_change_supporter_security', 'nonce' ) ?>
                                                                                <input type="hidden" name="ticket_id"
                                                                                       value="<?php echo esc_attr( $ticket_id ) ?>">
                                                                                <div class="form-control-group">
                                                                                    <label class="form-control-label"
                                                                                           for="supporter">
																						<?php esc_html_e( 'Assigned', 'sts' ) ?>
                                                                                    </label>
                                                                                    <select class="form-control"
                                                                                            id="supporter"
                                                                                            name="supporter">
                                                                                        <option value="" selected>
																							<?php esc_html_e( 'Assign supporter', 'sts' ) ?>
                                                                                        </option>
																						<?php if ( $supporters ):
																							foreach ( $supporters as $supporter ):
																								?>
                                                                                                <option <?php selected( $supporter->ID, $ticket->supporter_id ) ?>
                                                                                                        value="<?php echo esc_attr( $supporter->ID ); ?>">
																									<?php echo esc_html( $supporter->display_name ) ?>
                                                                                                </option>
																							<?php endforeach;
																						endif; ?>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="button-group">
                                                                                    <div class="button-group__item">
                                                                                        <button type="submit"
                                                                                                class="btn btn-primary">
																							<?php esc_html_e( 'Update', 'sts' ) ?>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="button-group__item">
                                                                                        <button type="button"
                                                                                                class="btn btn-default button-cancel-update">
																							<?php esc_html_e( 'Cancel', 'sts' ) ?>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
																<?php endif; ?>
                                                            </div>
                                                        </div>
													<?php endif; ?>
                                                </div>

                                                <div class="ticket-metadata__customer">
                                                    <div class="sts-sidebar__wrapper">
                                                        <div class="ticket-metadata__item">
                                                    <span class="ticket-metadata__label">
                                                        <?php esc_html_e( 'Customer', 'sts' ) ?>
                                                    </span>

                                                            <div class="ticket-metadata__content">
                                                                <span class="dashicons dashicons-businessperson"></span>
                                                                <a href="<?php echo esc_url( sts_support_page_url( 'customer-details/?customer_id=' . $ticket->customer_id ) ); ?>">
																	<?php echo esc_html( $customer->display_name ); ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="ticket-metadata__item">
                                                    <span class="ticket-metadata__label">
                                                        <?php esc_html_e( 'Contact', 'sts' ) ?>
                                                    </span>
                                                            <div class="ticket-metadata__content">
                                                                <span class="dashicons dashicons-email"></span>
                                                                <span title="<?php echo esc_attr( $customer->user_email ) ?>">
                                                            <?php echo esc_html( $customer->user_email ); ?>
                                                        </span>
                                                            </div>
                                                        </div>
                                                        <div class="ticket-metadata__item">
                                                            <span class="ticket-metadata__label">
                                                                <?php esc_html_e( 'Created date', 'sts' ) ?>
                                                            </span>
                                                            <div class="ticket-metadata__content">
                                                                <span class="dashicons dashicons-calendar-alt"></span>
																<?php echo date( 'F d,Y', strtotime( $ticket->created_date ) ); ?>
                                                            </div>
                                                        </div>
														<?php
														if ( ! isset( $ticket->purchasecode_id ) || $ticket->purchasecode_id == 0 || $ticket->purchasecode_id == null || ! has_filter( 'ena_section_purchase_code' ) ) {
															$theme = STS()->db()->themes()->getting_theme_by_id( $ticket->theme_id );
															?>
                                                            <div class="ticket-metadata__item">
                                                            <span class="ticket-metadata__label">
                                                                <?php esc_html_e( 'Category', 'sts' ) ?>
                                                            </span>
                                                                <div class="ticket-metadata__content">
                                                                    <span class="dashicons dashicons-calendar-alt"></span>
																	<?php echo esc_html( $theme->theme_name ) ?>
                                                                </div>
                                                            </div>
															<?php
														}
														?>
														<?php $numb_message = STS()->db()->messages()->count_message_by_ticket_id( $ticket_id );
														if ( ( ( $numb_message == 0 && $current_user->ID == $ticket->customer_id ) || in_array( 'administrator', $user_roles ) ) ): ?>
                                                            <a href="#"
                                                               data-sts-ladda="true"
                                                               data-sts-action="sts_delete_ticket"
                                                               data-sts-action-param="<?php echo esc_attr( json_encode( array(
																   'id'    => $ticket_id,
																   'nonce' => wp_create_nonce( 'sts_frontend_delete_ticket' )
															   ) ) ) ?>"
                                                               data-sts-confirm="<?php esc_attr_e( 'Are you sue delete this ticket?', 'sts' ) ?>"
                                                               class="ticket-metadata__action delete sts-delete-ticket">
																<?php esc_html_e( 'Delete this ticket', 'sts' ) ?>
                                                            </a>
														<?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
										<?php if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ) : ?>
                                            <div class="sts-sidebar__item sts-block">
                                                <h4 class="sts-block__title list-note-title">
													<?php esc_html_e( 'Listing Note', 'sts' ) ?>
                                                    <a href="#"
                                                       class="new-note"
                                                       data-ticket="<?php echo esc_attr( $ticket_id ); ?>"
                                                       title="<?php esc_attr_e( 'Add new note', 'sts' ) ?>">
                                                        <span class="dashicons dashicons-plus-alt"></span>
                                                    </a>
                                                </h4>
                                                <div class="note">
                                                    <div class="note__items" id="note">
														<?php $notes = STS()->db()->notes()->get_note_by_ticket_id( $ticket_id );
														if ( $notes ):
															foreach ( $notes as $note ):
																$user_note = get_user_by( 'ID', $note->user_id ) ?>
																<?php STS()->get_template( 'ticket-details/note-item.php', array(
																'note'  => array(
																	'note_id'      => $note->id,
																	'message'      => $note->message,
																	'created_date' => $note->created_date,
																	'name'         => $user_note->display_name,
																	'user_id'      => $note->user_id
																),
																'nonce' => wp_create_nonce( 'sts_delete_note_security' )
															) ) ?>
															<?php endforeach;
														endif; ?>
                                                    </div>
                                                    <div class="note__new-form" id="note__new-form">
                                                        <form method="post" action=""
                                                              data-sts-callback="STS.showClosedContent"
                                                              data-sts-form-action="sts_save_note"
                                                              data-sts-ladda="true">
                                                            <div class="form__message"
                                                                 id="sts-message-note"></div>
															<?php wp_nonce_field( 'sts_new_note_security', 'nonce' ); ?>
                                                            <input type="hidden" name="ticket_id"
                                                                   value="<?php echo esc_attr( $ticket_id ) ?>">
                                                            <input type="hidden" name="supporter_id"
                                                                   value="<?php echo esc_attr( $current_user->ID ) ?>">
                                                            <div class="form-control-group">
                                                <textarea class="form-control" id="input-note" name="note_content"
                                                          placeholder="<?php esc_attr_e( 'Post a note', 'sts' ) ?>"></textarea>
                                                            </div>
                                                            <div class="button-group">
                                                                <div class="button-group__item">
                                                                    <button class="btn btn-primary"
                                                                            id="post-note" type="submit">
																		<?php esc_html_e( 'Post note', 'sts' ) ?>
                                                                    </button>
                                                                </div>
                                                                <div class="button-group__item">
                                                                    <a href="#" class="btn btn-default button-cancel">
																		<?php esc_html_e( 'Cancel', 'sts' ) ?>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
										<?php endif; ?>
										<?php
										do_action( 'sts_ticket_details_widget', $ticket_id )
										?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
			<?php
			endif;
		endif;
	endif;
endif;
