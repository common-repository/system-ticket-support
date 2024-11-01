<div class="form-control-group">
    <label for="product" class="form-control-label">
		<?php esc_html_e( "Category*", "sts" ) ?>
    </label>
    <select class="form-control" id="product" name="theme" required
            data-user="<?php echo esc_attr( $current_user ) ?>">
        <option value="" selected>
			<?php esc_html_e( "Choose category*", "sts" ) ?>
        </option>
		<?php
		if ( $themes ):
			foreach ( $themes as $theme ):
				?>
                <option value="<?php echo esc_attr( $theme->theme_id ); ?>">
					<?php echo esc_html( $theme->theme_name ); ?>
                </option>
			<?php endforeach;
		endif; ?>
    </select>
</div>