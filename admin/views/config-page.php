<?php
/**
 * Admin view file.
 *
 * @package yoast_comment_hacks\admin
 */

/**
 * Outputs the full option string.
 *
 * @param string $option
 * @param bool   $return
 *
 * @return string
 */
function ch_option_name( $option, $return = false ) {
	$output = esc_attr( YoastCommentHacks::$option_name ) . '[' . esc_attr( $option ) . ']';
	if ( $return ) {
		return $output;
	}
	echo $output;
}

?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Yoast Comment Hacks', 'yoast-comment-hacks' ); ?></h2>

		<h2 class="nav-tab-wrapper" id="yoast-tabs">
			<a class="nav-tab nav-tab-active" id="comment-length-tab"
			   href="#top#comment-length"><?php esc_html_e( 'Comment length', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab nav-tab-active" id="comment-policy-tab"
			   href="#top#comment-policy"><?php esc_html_e( 'Comment policy', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="email-links-tab"
			   href="#top#email-links"><?php esc_html_e( 'Email links', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="comment-redirect-tab"
			   href="#top#comment-redirect"><?php esc_html_e( 'Comment redirect', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="clean-emails-tab"
			   href="#top#clean-emails"><?php esc_html_e( 'Clean emails', 'yoast-comment-hacks' ); ?></a>
		</h2>

		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" id="yoast-ch-conf" method="post">
			<?php settings_fields( YoastCommentHacks::$option_name ); ?>

			<div id="comment-length" class="yoasttab active">
				<h3><?php esc_html_e( 'Minimum comment length', 'yoast-comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Users that try to submit a comment smaller than the length you set below will get an error immediately. The text of that error is specified below too.', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label
									for="mincomlength"><?php esc_html_e( 'Minimum length', 'yoast-comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input type="number" class="small-text" min="5" max="255"
								   value="<?php echo esc_attr( $this->options['mincomlength'] ); ?>"
								   name="<?php ch_option_name( 'mincomlength' ); ?>"
								   id="mincomlength"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="mincomlengtherror"><?php esc_html_e( 'Error message for comment that is too short', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
					<textarea rows="4" cols="80"
							  name="<?php ch_option_name( 'mincomlengtherror' ); ?>"
							  id="mincomlengtherror"><?php echo esc_html( $this->options['mincomlengtherror'] ); ?></textarea>
						</td>
					</tr>
				</table>

				<h3><?php esc_html_e( 'Maximum comment length', 'yoast-comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Users that try to submit a comment longer than the length you set below will get an error immediately. The text of that error is specified below too.', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="maxcomlength"><?php esc_html_e( 'Maximum length', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<input type="number" class="small-text" min="0"
								   value="<?php echo esc_attr( $this->options['maxcomlength'] ); ?>"
								   name="<?php ch_option_name( 'maxcomlength' ); ?>"
								   id="maxcomlength"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label
									for="maxcomlengtherror"><?php esc_html_e( 'Error message for comment that is too long', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
					<textarea rows="4" cols="80"
							  name="<?php ch_option_name( 'maxcomlengtherror' ); ?>"
							  id="maxcomlengtherror"><?php echo esc_html( $this->options['maxcomlengtherror'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="comment-policy" class="yoasttab">
				<h3><?php esc_html_e( 'Comment policy', 'yoast-comment-hacks' ); ?></h3>
				<p><?php esc_html_e( 'This feature allows you to institute a comment policy for your site. To enable it, set a text in the input field below.', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="comment_policy"><?php esc_html_e( 'Comment policy', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<input type="checkbox" <?php checked( $this->options['comment_policy'] ); ?>
								   name="<?php ch_option_name( 'comment_policy' ); ?>" id="comment_policy"/>
							<p>
								<label for="comment_policy"><?php esc_html_e( 'Enable the comment policy functionality.', 'yoast-comment-hacks' ); ?></label>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="comment_policy_page"><?php esc_html_e( 'Comment policy page', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<?php
							// A dropdown of all pages in the current WP install.
							$selected = 0;
							if ( isset( $this->options['comment_policy_page'] ) ) {
								$selected = $this->options['comment_policy_page'];
							}
							wp_dropdown_pages(
								array(
									'depth'             => 0,
									'id'                => 'comment_policy_page',
									'name'              => ch_option_name( 'comment_policy', true ),
									'option_none_value' => 0,
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $selected is not put out, only compared against.
									'selected'          => $selected,
									'show_option_none'  => esc_html__( 'Select comment policy page', 'yoast-comment-hacks' ),
								)
							);
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="comment_policy_text"><?php esc_html_e( 'Comment policy line', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<input type="text" class="regular-text"
								   value="<?php echo esc_attr( $this->options['comment_policy_text'] ); ?>"
								   name="<?php ch_option_name( 'comment_policy_text' ); ?>"
								   id="comment_policy_text"/>
							<p><?php esc_html_e( 'Text that appears along with a checkbox above the comment submit button.', 'yoast-comment-hacks' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label
									for="comment_policyesc_html_error"><?php esc_html_e( 'Error message for not checking comment policy checkbox', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<textarea rows="4" cols="80" name="<?php ch_option_name( 'comment_policy_error' ); ?>" id="comment_policy"><?php echo esc_html( $this->options['comment_policy_error'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="email-links" class="yoasttab">
				<h3><?php esc_html_e( 'Email links', 'yoast-comment-hacks' ); ?></h3>

				<p>
					<?php
					/* translators: %s expands to an email button icon */
					printf( esc_html__( 'This plugin adds an "E-mail" action link on the comments overview page as well as an email all commenters button (%s) on individual post pages in the admin bar. You can customize the default messages here.', 'yoast-comment-hacks' ), '<span class="dashicons dashicons-email-alt"></span>' );
					?>
				</p>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">
							<label for="email_body"><?php esc_html_e( 'E-mail subject', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<input type="text" class="regular-text"
								   name="<?php ch_option_name( 'email_subject' ); ?>"
								   id="email_subject"
								   value="<?php echo esc_attr( $this->options['email_subject'] ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="email_body"><?php esc_html_e( 'E-mail body', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
					<textarea rows="4" cols="100" name="<?php ch_option_name( 'email_body' ); ?>"
							  id="email_body"><?php echo esc_html( $this->options['email_body'] ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label
									for="massesc_html_email_body"><?php esc_html_e( 'E-mail all commenters body', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
					<textarea rows="4" cols="100" name="<?php ch_option_name( 'mass_email_body' ); ?>"
							  id="mass_email_body"><?php echo esc_html( $this->options['mass_email_body'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="comment-redirect" class="yoasttab">
				<h3><?php esc_html_e( 'Redirect first time commenters', 'yoast-comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Select the page below that a first time commenter should be redirected to', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">
							<label for="redirect_page"><?php esc_html_e( 'Redirect to', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<?php

							// A dropdown of all pages in the current WP install.
							wp_dropdown_pages(
								array(
									'depth'             => 0,
									'id'                => 'redirect_page',
									'name'              => ch_option_name( 'redirect_page', true ),
									'option_none_value' => 0,
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $selected is not put out, only compared against.
									'selected'          => isset( $this->options['redirect_page'] ) ? $this->options['redirect_page'] : 0,
									'show_option_none'  => esc_html__( 'Don\'t redirect first time commenters', 'yoast-comment-hacks' ),
								)
							);

							if ( isset( $this->options['redirect_page'] ) && 0 !== $this->options['redirect_page'] ) {
								echo '<br><br><a target="_blank" href="' . esc_url( get_permalink( $this->options['redirect_page'] ) ) . '">' . esc_html__( 'Current redirect page', 'yoast-comment-hacks' ) . '</a>';
							}

							?>
						</td>
					</tr>
				</table>
			</div>
			<div id="clean-emails" class="yoasttab">
				<h3><?php esc_html_e( 'Clean Emails', 'yoast-comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Checking this option will make your default comment notification and moderation emails a lot cleaner.', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">
							<label
									for="clean_emails"><?php esc_html_e( 'Clean comment emails', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td><input type="checkbox" id="clean_emails"
								   name="<?php ch_option_name( 'clean_emails' ); ?>" <?php checked( $this->options['clean_emails'] ); ?> />
						</td>
					</tr>
				</table>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>

<?php

do_action( 'yoast_ch_admin_footer' );
