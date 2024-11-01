<?php
foreach ( $contents as $content ):
	if ( in_array( 'administrator', $content['roles'] ) ||
	     in_array( 'leader_supporter', $content['roles'] ) ||
	     in_array( 'supporter', $content['roles'] ) ):
		?>
		<div class="single-ticket__container single-ticket__container--supporter"
		     id="message-<?php echo esc_attr( $content['id'] ) ?>">
			<div class="single-ticket__wrapper">
				<div class="single-ticket__content">
					<?php STS()->get_template( 'ticket-details/ticket-author-info.php',
						array(
							'name'         => $content['name'],
							'created_date' => $content['created_date'],
							'profile_url'  => $content['profile_url'],
							'is_question'  => $content['is_question'],
							'message_url'  => $content['message_url'],
							'id'           => $content['id']
						) ) ?>
					<?php STS()->get_template( 'ticket-details/ticket-content.php',
						array(
							'message_id'  => $content['id'],
							'message'     => $content['message'],
							'attachments' => $content['attachments']
						) );
					?>
					<?php if ( ( $content == max( $contents ) && $content['current_user'] ) || $content['admin'] ) : ?>
						<?php STS()->get_template( 'ticket-details/message-action-button.php',
							array(
								'is_question' => 0,
								'ticket_id'   => $content['ticket_id'],
								'message_id'  => $content['id'],
								'nonce'       => $nonce_delete,
								'nonce_edit'  => $nonce_edit
							)
						);
						?>
					<?php endif; ?>
				</div>
				<?php STS()->get_template( 'ticket-details/ticket-avatar.php',
					array(
						'avatar_url'  => $content['avatar'],
						'profile_url' => $content['profile_url']
					) ) ?>
			</div>
			<?php STS()->get_template( 'ticket-details/message-action.php',
				array(
					'ticket_id'  => $content['ticket_id'],
					'message_id' => $content['id'],
				)
			); ?>
		</div>
	<?php else: ?>
		<div class="single-ticket__container" id="message-<?php echo esc_attr( $content['id'] ) ?>">
			<div class="single-ticket__wrapper">
				<?php STS()->get_template( 'ticket-details/ticket-avatar.php',
					array(
						'avatar_url'   => $content['avatar'],
						'name'         => $content['name'],
						'created_date' => $content['created_date'],
						'profile_url'  => $content['profile_url']
					) ) ?>
				<div class="single-ticket__content">
					<?php STS()->get_template( 'ticket-details/ticket-author-info.php',
						array(
							'name'         => $content['name'],
							'created_date' => $content['created_date'],
							'profile_url'  => $content['profile_url'],
							'is_question'  => $content['is_question'],
							'message_url'  => $content['message_url'],
							'id'           => $content['id']
						) ) ?>
					<?php STS()->get_template( 'ticket-details/ticket-content.php',
						array(
							'message_id'  => $content['id'],
							'message'     => $content['message'],
							'attachments' => $content['attachments']
						) );
					?>
					<?php if ( ( $content == max( $contents ) && $content['current_user'] ) || $content['admin'] ) : ?>
						<?php STS()->get_template( 'ticket-details/message-action-button.php',
							array(
								'is_question' => 0,
								'ticket_id'   => $content['ticket_id'],
								'message_id'  => $content['id'],
								'nonce'       => $nonce_delete,
								'nonce_edit'  => $nonce_edit
							)
						);

						?>
					<?php endif; ?>
				</div>
			</div>
			<?php STS()->get_template( 'ticket-details/message-action.php',
				array(
					'ticket_id'  => $content['ticket_id'],
					'message_id' => $content['id'],
				)
			); ?>
		</div>
	<?php endif;
endforeach; ?>

