<?php
/**
 * @package yoast_comment_hacks\admin
 */
?>
<div class="wrap">
	<h2><?php _e( 'Yoast Comment Hacks', 'yoast-comment-hacks' ); ?></h2>

	<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
		<?php settings_fields( $this->option_name ); ?>

		<h3><?php _e( 'Minimum comment length', 'yoast-comment-hacks' ); ?></h3>

		<p><?php _e( 'Users that try to submit a comment smaller than the length you set below will get an error immediately. The text of that error is specified below too.', 'yoast-comment-hacks' ); ?></p>
		<table class="form-table">
			<tr valign="top">
				<th scrope="row">
					<label
						for="mincomlength"><?php _e( 'Minimum length', 'yoast-comment-hacks' ); ?>
						:</label>
				</th>
				<td>
					<input type="number" class="small-text" min="5" max="255"
					       value="<?php echo esc_attr( $this->options['mincomlength'] ); ?>"
					       name="<?php echo esc_attr( $this->option_name ); ?>[mincomlength]" id="mincomlength"/>
				</td>
			</tr>
			<tr valign="top">
				<th scrope="row">
					<label for="mincomlengtherror"><?php _e( 'Error message:', 'yoast-comment-hacks' ); ?></label>
				</th>
				<td>
					<textarea rows="5" cols="50" name="<?php echo esc_attr( $this->option_name ); ?>[mincomlengtherror]"
					          id="mincomlengtherror"><?php echo esc_html( $this->options['mincomlengtherror'] ); ?></textarea>
				</td>
			</tr>

		</table>

		<h3><?php _e( 'Redirect first time commenters', 'yoast-comment-hacks' ); ?></h3>

		<p><?php _e( 'Select the page below that a first time commenter should be redirected to:', 'yoast-comment-hacks' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row" valign="top">
					<label for="redirect_page"><?php _e( 'Redirect to', 'yoast-comment-hacks' ); ?>:</label>
				</th>
				<td><?php

					// A dropdown of all pages in the current WP install.
					wp_dropdown_pages( array(
						'depth'             => 0,
						'id'                => 'redirect_page',
						'name'              => $this->option_name . '[redirect_page]',
						'option_none_value' => 0,
						'selected'          => isset( $this->options['redirect_page'] ) ? $this->options['redirect_page'] : 0,
						'show_option_none'  => __( 'Don\'t redirect first time commenters', 'yoast-comment-hacks' ),
					) );

					?></td>
			</tr>
		</table>

		<h3><?php _e( 'Clean Emails', 'yoast-comment-hacks' ); ?></h3>

		<p><?php _e( 'Checking this option will make your default comment notification and moderation emails a lot cleaner.', 'yoast-comment-hacks' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row" valign="top">
					<label for="clean_emails"><?php _e( 'Clean comment emails', 'yoast-comment-hacks' ); ?>:</label>
				</th>
				<td><input type="checkbox" id="clean_emails"
				           name="<?php echo esc_attr( $this->option_name ); ?>[clean_emails]" <?php checked( $this->options['clean_emails'] ); ?> />
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" class="button-primary"
			       value="<?php esc_attr_e( 'Update Settings &raquo;', 'yoast-comment-hacks' ); ?>"/>
		</p>
	</form>
</div>