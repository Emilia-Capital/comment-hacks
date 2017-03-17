<?php
/**
 * @package yoast_comment_hacks\admin
 */

$option_name = esc_attr( YoastCommentHacks::$option_name );
?>
	<div class="wrap">
		<h2><?php _e( 'Yoast Comment Hacks', 'yoast-comment-hacks' ); ?></h2>

		<h2 class="nav-tab-wrapper" id="yoast-tabs">
			<a class="nav-tab nav-tab-active" id="comment-length-tab"
			   href="#top#comment-length"><?php _e( 'Comment length', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="email-links-tab"
			   href="#top#email-links"><?php _e( 'Email links', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="comment-redirect-tab"
			   href="#top#comment-redirect"><?php _e( 'Comment redirect', 'yoast-comment-hacks' ); ?></a>
			<a class="nav-tab" id="clean-emails-tab"
			   href="#top#clean-emails"><?php _e( 'Clean emails', 'yoast-comment-hacks' ); ?></a>
		</h2>

		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" id="yoast-ch-conf" method="post">
			<?php settings_fields( $option_name ); ?>

			<div id="comment-length" class="yoasttab active">
				<h3><?php _e( 'Minimum comment length', 'yoast-comment-hacks' ); ?></h3>

				<p><?php _e( 'Users that try to submit a comment smaller than the length you set below will get an error immediately. The text of that error is specified below too.', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scrope="row">
							<label
								for="mincomlength"><?php _e( 'Minimum length', 'yoast-comment-hacks' ); ?>
							</label>
						</th>
						<td>
							<input type="number" class="small-text" min="5" max="255"
							       value="<?php echo esc_attr( $this->options['mincomlength'] ); ?>"
							       name="<?php echo $option_name; ?>[mincomlength]"
							       id="mincomlength"/>
						</td>
					</tr>
					<tr valign="top">
						<th scrope="row">
							<label
								for="mincomlengtherror"><?php _e( 'Error message for comment that is too short', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
					<textarea rows="4" cols="80"
					          name="<?php echo $option_name; ?>[mincomlengtherror]"
					          id="mincomlengtherror"><?php echo esc_html( $this->options['mincomlengtherror'] ); ?></textarea>
						</td>
					</tr>
				</table>

                <h3><?php _e( 'Maximum comment length', 'yoast-comment-hacks' ); ?></h3>

                <p><?php _e( 'Users that try to submit a comment longer than the length you set below will get an error immediately. The text of that error is specified below too.', 'yoast-comment-hacks' ); ?></p>
                <table class="form-table">
                    <tr valign="top">
                        <th scrope="row">
                            <label
                                    for="maxcomlength"><?php _e( 'Maximum length', 'yoast-comment-hacks' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number" class="small-text" min="5"
                                   value="<?php echo esc_attr( $this->options['maxcomlength'] ); ?>"
                                   name="<?php echo $option_name; ?>[maxcomlength]"
                                   id="maxcomlength"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scrope="row">
                            <label
                                    for="maxcomlengtherror"><?php _e( 'Error message for comment that is too long', 'yoast-comment-hacks' ); ?></label>
                        </th>
                        <td>
					<textarea rows="4" cols="80"
                              name="<?php echo $option_name; ?>[maxcomlengtherror]"
                              id="maxcomlengtherror"><?php echo esc_html( $this->options['maxcomlengtherror'] ); ?></textarea>
                        </td>
                    </tr>
                </table>
			</div>

			<div id="email-links" class="yoasttab">
				<h3><?php _e( 'Email links', 'yoast-comment-hacks' ); ?></h3>

				<p><?php
					/* translators: %s expands to an email button icon */
					printf( __( 'This plugin adds an "E-mail" action link on the comments overview page as well as an email all commenters button (%s) on individual post pages in the admin bar. You can customize the default messages here.', 'yoast-comment-hacks' ), '<span class="dashicons dashicons-email-alt"></span>' );
					?></p>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">
							<label for="email_body"><?php _e( 'E-mail subject', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
							<input type="text" name="<?php echo $option_name; ?>[email_subject]"
							       id="email_subject"
							       value="<?php echo esc_attr( $this->options['email_subject'] ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="email_body"><?php _e( 'E-mail body', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
					<textarea rows="4" cols="100" name="<?php echo $option_name; ?>[email_body]"
					          id="email_body"><?php echo esc_html( $this->options['email_body'] ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label
								for="mass_email_body"><?php _e( 'E-mail all commenters body', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td>
					<textarea rows="4" cols="100" name="<?php echo $option_name; ?>[mass_email_body]"
					          id="mass_email_body"><?php echo esc_html( $this->options['mass_email_body'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="comment-redirect" class="yoasttab">
				<h3><?php _e( 'Redirect first time commenters', 'yoast-comment-hacks' ); ?></h3>

				<p><?php _e( 'Select the page below that a first time commenter should be redirected to', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">
							<label for="redirect_page"><?php _e( 'Redirect to', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td><?php

							// A dropdown of all pages in the current WP install.
							wp_dropdown_pages( array(
								'depth'             => 0,
								'id'                => 'redirect_page',
								'name'              => $option_name . '[redirect_page]',
								'option_none_value' => 0,
								'selected'          => isset( $this->options['redirect_page'] ) ? $this->options['redirect_page'] : 0,
								'show_option_none'  => __( 'Don\'t redirect first time commenters', 'yoast-comment-hacks' ),
							) );

							if ( isset( $this->options['redirect_page'] ) && 0 !== $this->options['redirect_page'] ) {
								echo '<br><br><a target="_blank" href="' . get_permalink( $this->options['redirect_page'] ) . '">' . __( 'Current redirect page', 'yoast-comment-hacks' ) . '</a>';
							}

							?></td>
					</tr>
				</table>
			</div>
			<div id="clean-emails" class="yoasttab">
				<h3><?php _e( 'Clean Emails', 'yoast-comment-hacks' ); ?></h3>

				<p><?php _e( 'Checking this option will make your default comment notification and moderation emails a lot cleaner.', 'yoast-comment-hacks' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">
							<label
								for="clean_emails"><?php _e( 'Clean comment emails', 'yoast-comment-hacks' ); ?></label>
						</th>
						<td><input type="checkbox" id="clean_emails"
						           name="<?php echo $option_name; ?>[clean_emails]" <?php checked( $this->options['clean_emails'] ); ?> />
						</td>
					</tr>
				</table>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>

<?php do_action( 'yoast_ch_admin_footer' );
