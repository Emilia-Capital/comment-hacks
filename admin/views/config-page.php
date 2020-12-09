<?php
/**
 * Config page admin view.
 *
 * @package YoastCommentHacks\admin
 */

use Yoast\WP\Comment\Inc\Hacks;

$yoast_comment_option_name = Hacks::$option_name;
?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Yoast Comment Hacks', 'yoast-comment-hacks' ); ?></h2>

		<h2 class="nav-tab-wrapper" id="yoast-tabs">
			<a class="nav-tab nav-tab-active" id="comment-length-tab"
					href="#top#comment-length"><?php esc_html_e( 'Comment length', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="email-links-tab"
					href="#top#email-links"><?php esc_html_e( 'Email links', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="comment-redirect-tab"
					href="#top#comment-redirect"><?php esc_html_e( 'Comment redirect', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="clean-emails-tab"
					href="#top#clean-emails"><?php esc_html_e( 'Clean emails', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="forward-emails-tab"
					href="#top#forward-emails"><?php esc_html_e( 'Forward emails', 'yoast-comment-hacks' ); ?></a>
		</h2>

		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" id="yoast-ch-conf" method="post">
			<?php settings_fields( $yoast_comment_option_name ); ?>

			<div id="comment-length" class="yoasttab active">
				<h3><?php esc_html_e( 'Minimum comment length', 'yoast-comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Users that try to submit a comment smaller than the length you set below will get an error immediately. The text of that error is specified below too.', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scrope="row">
							<label for="mincomlength"><?php esc_html_e( 'Minimum length', 'yoast-comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input type="number" class="small-text" min="5" max="255"
									value="<?php echo esc_attr( $this->options['mincomlength'] ); ?>"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[mincomlength]' ); ?>"
									id="mincomlength"/>
						</td>
					</tr>
					<tr>
						<th scrope="row">
							<label for="mincomlengtherror"><?php esc_html_e( 'Error message for comment that is too short', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<textarea rows="4" cols="80"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[mincomlengtherror]' ); ?>"
									id="mincomlengtherror"><?php echo esc_html( $this->options['mincomlengtherror'] ); ?></textarea>
						</td>
					</tr>
				</table>

				<h3><?php esc_html_e( 'Maximum comment length', 'yoast-comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Users that try to submit a comment longer than the length you set below will get an error immediately. The text of that error is specified below too.', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scrope="row">
							<label for="maxcomlength"><?php esc_html_e( 'Maximum length', 'yoast-comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input type="number" class="small-text" min="5"
									value="<?php echo esc_attr( $this->options['maxcomlength'] ); ?>"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[maxcomlength]' ); ?>"
									id="maxcomlength"/>
						</td>
					</tr>
					<tr>
						<th scrope="row">
							<label for="maxcomlengtherror"><?php esc_html_e( 'Error message for comment that is too long', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<textarea rows="4" cols="80"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[maxcomlengtherror]' ); ?>"
									id="maxcomlengtherror"><?php echo esc_html( $this->options['maxcomlengtherror'] ); ?></textarea>
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
						<th scope="row">
							<label for="email_body"><?php esc_html_e( 'E-mail subject', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<input type="text"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[email_subject]' ); ?>"
									id="email_subject" class="regular-text"
									value="<?php echo esc_attr( $this->options['email_subject'] ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="email_body"><?php esc_html_e( 'E-mail body', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<textarea rows="4" cols="100"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[email_body]' ); ?>"
									id="email_body"><?php echo esc_html( $this->options['email_body'] ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label
									for="mass_email_body"><?php esc_html_e( 'E-mail all commenters body', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<textarea rows="4" cols="100"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[mass_email_body]' ); ?>"
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
						<th scope="row">
							<label for="redirect_page"><?php esc_html_e( 'Redirect to', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<?php
							// A dropdown of all pages in the current WP install.
							wp_dropdown_pages(
								[
									'depth'             => 0,
									'id'                => 'redirect_page',
									// phpcs:ignore WordPress.Security.EscapeOutput -- This is a hard-coded string, just passed around as a variable.
									'name'              => $yoast_comment_option_name . '[redirect_page]',
									'option_none_value' => 0,
									'selected'          => ( isset( $this->options['redirect_page'] ) ? (int) $this->options['redirect_page'] : 0 ),
									'show_option_none'  => esc_html__( 'Don\'t redirect first time commenters', 'yoast-comment-hacks' ),
								]
							);

							if ( isset( $this->options['redirect_page'] ) && $this->options['redirect_page'] !== 0 ) {
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
						<th scope="row">
							<label for="clean_emails"><?php esc_html_e( 'Clean comment emails', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td><input type="checkbox" id="clean_emails"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[clean_emails]' ); ?>" <?php checked( $this->options['clean_emails'] ); ?> />
						</td>
					</tr>
				</table>
			</div>

			<div id="forward-emails" class="yoasttab">
				<h3><?php esc_html_e( 'Forward Emails', 'yoast-comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Allows you to set up a system whereby comments are forwarded to an email address (for instance for your support team) and then trashed.', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="forward-emails-email"><?php esc_html_e( 'Forward email to', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td><input type="email" id="forward-emails-email" class="regular-text"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[forward_email]' ); ?>"
									value="<?php echo esc_attr( $this->options['forward_email'] ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label
									for="forward-emails-name"><?php esc_html_e( 'Forward "From" name', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td><input type="text" id="forward-emails-name" class="regular-text"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[forward_name]' ); ?>"
									value="<?php echo esc_attr( $this->options['forward_name'] ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="forward-emails-from-email"><?php esc_html_e( 'Forward "From" email address', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td><input type="email" id="forward-emails-from-email" class="regular-text"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[forward_from_email]' ); ?>"
									value="<?php echo esc_attr( $this->options['forward_from_email'] ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="forward-emails-subject"><?php esc_html_e( 'Forward subject', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td><input type="text" id="forward-emails-subject" class="regular-text"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[forward_subject]' ); ?>"
									value="<?php echo esc_attr( $this->options['forward_subject'] ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="forward-emails-intro-extra"><?php esc_html_e( 'Forward intro additional text', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td><textarea rows="4" cols="80" id="forward-emails-extra"
									name="<?php echo esc_attr( $yoast_comment_option_name . '[forward_extra]' ); ?>"><?php echo esc_html( $this->options['forward_extra'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>

<?php
/**
 * Action hook to allow other plugins to add additional information to the
 * Yoast Comment Hacks admin page.
 *
 * @deprecated 1.6.0. Use the {@see 'Yoast\WP\Comment\admin_footer'} action instead.
 */
do_action_deprecated( 'yoast_ch_admin_footer', [], 'Yoast Comment 1.6.0', 'Yoast\WP\Comment\admin_footer' );

/**
 * Action hook to allow other plugins to add additional information to the
 * Yoast Comment Hacks admin page.
 *
 * @since 1.6.0
 */
do_action( 'Yoast\WP\Comment\admin_footer' );
