<?php
/**
 * Config page admin view.
 *
 * @var Admin $this
 */

use EmiliaProjects\WP\Comment\Admin\Admin;
use EmiliaProjects\WP\Comment\Inc\Hacks;

?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Comment Hacks', 'comment-hacks' ); ?></h2>

		<h2 class="nav-tab-wrapper" id="emiliaprojects-tabs">
			<a class="nav-tab nav-tab-active" id="comment-length-tab" href="#top#comment-length">
				<?php esc_html_e( 'Comment length', 'comment-hacks' ); ?>
			</a>
			<a class="nav-tab" id="comment-policy-tab" href="#top#comment-policy">
				<?php esc_html_e( 'Comment policy', 'comment-hacks' ); ?>
			</a>
			<a class="nav-tab" id="email-links-tab" href="#top#email-links">
				<?php esc_html_e( 'Email links', 'comment-hacks' ); ?>
			</a>
			<a class="nav-tab" id="comment-redirect-tab" href="#top#comment-redirect">
				<?php esc_html_e( 'Comment redirect', 'comment-hacks' ); ?>
			</a>
			<a class="nav-tab" id="clean-emails-tab" href="#top#clean-emails">
				<?php esc_html_e( 'Clean emails', 'comment-hacks' ); ?>
			</a>
			<a class="nav-tab" id="forward-emails-tab" href="#top#forward-emails">
				<?php esc_html_e( 'Forward emails', 'comment-hacks' ); ?>
			</a>
			<a class="nav-tab" id="email-all-tab" href="#top#email-all">
				<?php esc_html_e( 'Email all commenters', 'comment-hacks' ); ?>
			</a>
		</h2>

		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" id="emiliaprojects-ch-conf" method="post">
			<?php settings_fields( Hacks::$option_name ); ?>

			<div id="comment-length" class="emiliaprojectstab active">
				<h3><?php esc_html_e( 'Minimum comment length', 'comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Users that try to submit a comment smaller than the length you set below will get an error immediately. The text of that error is specified below too.', 'comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="mincomlength">
								<?php esc_html_e( 'Minimum length', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="number"
								class="small-text"
								min="5"
								max="255"
								value="<?php echo esc_attr( $this->options['mincomlength'] ); ?>"
								name="<?php echo esc_attr( Hacks::$option_name . '[mincomlength]' ); ?>"
								id="mincomlength"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="mincomlengtherror">
								<?php esc_html_e( 'Error message for comment that is too short', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<textarea
								rows="4"
								cols="80"
								name="<?php echo esc_attr( Hacks::$option_name . '[mincomlengtherror]' ); ?>"
								id="mincomlengtherror"
							><?php echo esc_html( $this->options['mincomlengtherror'] ); ?></textarea>
						</td>
					</tr>
				</table>

				<h3><?php esc_html_e( 'Maximum comment length', 'comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Users that try to submit a comment longer than the length you set below will get an error immediately. The text of that error is specified below too.', 'comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="maxcomlength">
								<?php esc_html_e( 'Maximum length', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="number"
								class="small-text"
								min="5"
								value="<?php echo esc_attr( $this->options['maxcomlength'] ); ?>"
								name="<?php echo esc_attr( Hacks::$option_name . '[maxcomlength]' ); ?>"
								id="maxcomlength"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="maxcomlengtherror">
								<?php esc_html_e( 'Error message for comment that is too long', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<textarea
								rows="4"
								cols="80"
								name="<?php echo esc_attr( Hacks::$option_name . '[maxcomlengtherror]' ); ?>"
								id="maxcomlengtherror"
							><?php echo esc_html( $this->options['maxcomlengtherror'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="comment-policy" class="emiliaprojectstab">
				<h3><?php esc_html_e( 'Comment policy', 'comment-hacks' ); ?></h3>
				<p><?php esc_html_e( 'This feature allows you to institute a comment policy for your site. To enable it, set a text in the input field below.', 'comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="comment_policy">
								<?php esc_html_e( 'Comment policy', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="checkbox"
								<?php checked( $this->options['comment_policy'] ); ?>
								name="<?php echo esc_attr( Hacks::$option_name . '[comment_policy]' ); ?>"
								id="comment_policy"
							/>
							<p>
								<label for="comment_policy">
									<?php esc_html_e( 'Enable the comment policy functionality.', 'comment-hacks' ); ?>
								</label>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="comment_policy_page">
								<?php esc_html_e( 'Comment policy page', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<?php
							wp_dropdown_pages(
								[
									'depth'             => 0,
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $selected is not put out, only compared against.
									'selected'          => ( isset( $this->options['comment_policy_page'] ) ? (int) $this->options['comment_policy_page'] : 0 ),
									'name'              => esc_attr( Hacks::$option_name . '[comment_policy_page]' ),
									'id'                => 'comment_policy_page',
									'show_option_none'  => esc_html__( 'Select comment policy page', 'comment-hacks' ),
									'option_none_value' => '',
								]
							);
							?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="comment_policy_text">
								<?php esc_html_e( 'Comment policy line', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="text"
								class="regular-text"
								value="<?php echo esc_attr( $this->options['comment_policy_text'] ); ?>"
								name="<?php echo esc_attr( Hacks::$option_name . '[comment_policy_text]' ); ?>"
								id="comment_policy_text"
							/>
							<p><?php esc_html_e( 'Text that appears along with a checkbox above the comment submit button.', 'comment-hacks' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="comment_policyesc_html_error">
								<?php esc_html_e( 'Error message for not checking comment policy checkbox', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<textarea
								rows="4"
								cols="80"
								name="<?php echo esc_attr( Hacks::$option_name . '[comment_policy_error]' ); ?>"
								id="comment_policy"
							><?php echo esc_html( $this->options['comment_policy_error'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="email-links" class="emiliaprojectstab">
				<h3><?php esc_html_e( 'Email links', 'comment-hacks' ); ?></h3>

				<p>
					<?php
					printf(
						/* translators: %s expands to an email button icon */
						esc_html__( 'This plugin adds an "E-mail" action link on the comments overview page as well as an email all commenters button (%s) on individual post pages in the admin bar. You can customize the default messages here.', 'comment-hacks' ),
						'<span class="dashicons dashicons-email-alt"></span>'
					);
					?>
				</p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="email_body">
								<?php esc_html_e( 'E-mail subject', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="text"
								name="<?php echo esc_attr( Hacks::$option_name . '[email_subject]' ); ?>"
								id="email_subject" class="regular-text"
								value="<?php echo esc_attr( $this->options['email_subject'] ); ?>"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="email_body">
								<?php esc_html_e( 'E-mail body', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<textarea
								rows="4"
								cols="100"
								name="<?php echo esc_attr( Hacks::$option_name . '[email_body]' ); ?>"
								id="email_body"
							><?php echo esc_html( $this->options['email_body'] ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="mass_email_body">
								<?php esc_html_e( 'E-mail all commenters body', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<textarea
								rows="4"
								cols="100"
								name="<?php echo esc_attr( Hacks::$option_name . '[mass_email_body]' ); ?>"
								id="mass_email_body"
							><?php echo esc_html( $this->options['mass_email_body'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="comment-redirect" class="emiliaprojectstab">
				<h3><?php esc_html_e( 'Redirect first time commenters', 'comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Select the page below that a first time commenter should be redirected to', 'comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="redirect_page">
								<?php esc_html_e( 'Redirect to', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<?php
							// A dropdown of all pages in the current WP install.
							wp_dropdown_pages(
								[
									'depth'             => 0,
									'id'                => 'redirect_page',
									// phpcs:ignore WordPress.Security.EscapeOutput -- This is a hard-coded string, just passed around as a variable.
									'name'              => Hacks::$option_name . '[redirect_page]',
									'option_none_value' => '',
									'selected'          => ( isset( $this->options['redirect_page'] ) ? (int) $this->options['redirect_page'] : 0 ),
									'show_option_none'  => esc_html__( 'Don\'t redirect first time commenters', 'comment-hacks' ),
								]
							);
							?>

							<?php if ( isset( $this->options['redirect_page'] ) && $this->options['redirect_page'] !== 0 ) : ?>
								<br>
								<br>
								<a target="_blank" href="<?php echo esc_url( get_permalink( (int) $this->options['redirect_page'] ) ); ?>">
									<?php esc_html_e( 'Current redirect page', 'comment-hacks' ); ?>
								</a>
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</div>
			<div id="clean-emails" class="emiliaprojectstab">
				<h3><?php esc_html_e( 'Clean Emails', 'comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Checking this option will make your default comment notification and moderation emails a lot cleaner.', 'comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="clean_emails">
								<?php esc_html_e( 'Clean comment emails', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="checkbox"
								id="clean_emails"
								name="<?php echo esc_attr( Hacks::$option_name . '[clean_emails]' ); ?>"
								<?php checked( $this->options['clean_emails'] ); ?> 
							/>
						</td>
					</tr>
				</table>
			</div>

			<div id="forward-emails" class="emiliaprojectstab">
				<h3><?php esc_html_e( 'Forward Emails', 'comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'Allows you to set up a system whereby comments are forwarded to an email address (for instance for your support team) and then trashed.', 'comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="forward-emails-email">
								<?php esc_html_e( 'Forward email to', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="email"
								id="forward-emails-email"
								class="regular-text"
								name="<?php echo esc_attr( Hacks::$option_name . '[forward_email]' ); ?>"
								value="<?php echo esc_attr( $this->options['forward_email'] ); ?>"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="forward-emails-name">
								<?php esc_html_e( 'Forward "From" name', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="text"
								id="forward-emails-name"
								class="regular-text"
								name="<?php echo esc_attr( Hacks::$option_name . '[forward_name]' ); ?>"
								value="<?php echo esc_attr( $this->options['forward_name'] ); ?>"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="forward-emails-from-email">
								<?php esc_html_e( 'Forward "From" email address', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="email"
								id="forward-emails-from-email"
								class="regular-text"
								name="<?php echo esc_attr( Hacks::$option_name . '[forward_from_email]' ); ?>"
								value="<?php echo esc_attr( $this->options['forward_from_email'] ); ?>"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="forward-emails-subject">
								<?php esc_html_e( 'Forward subject', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="text"
								id="forward-emails-subject"
								class="regular-text"
								name="<?php echo esc_attr( Hacks::$option_name . '[forward_subject]' ); ?>"
								value="<?php echo esc_attr( $this->options['forward_subject'] ); ?>"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="forward-emails-intro-extra">
								<?php esc_html_e( 'Forward intro additional text', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<textarea
								rows="4"
								cols="80"
								id="forward-emails-extra"
								name="<?php echo esc_attr( Hacks::$option_name . '[forward_extra]' ); ?>"
							><?php echo isset( $this->options['forward_extra'] ) ? esc_html( $this->options['forward_extra'] ) : ''; ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="email-all" class="emiliaprojectstab">
				<h3><?php esc_html_e( 'Email all commenters', 'comment-hacks' ); ?></h3>

				<p><?php esc_html_e( 'This plugin comes with an "Email all commenters" admin bar button. You can disable it here.', 'comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="disable_email_all_commenters">
								<?php esc_html_e( 'Disable "Email all commenters" button', 'comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input
								type="checkbox"
								id="disable_email_all_commenters"
								name="<?php echo esc_attr( Hacks::$option_name . '[disable_email_all_commenters]' ); ?>"
								<?php checked( $this->options['disable_email_all_commenters'] ); ?>
							/>
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
 * Comment Hacks admin page.
 *
 * @since 1.6.0
 */
do_action( 'EmiliaProjects\WP\Comment\admin_footer' );
