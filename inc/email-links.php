<?php

namespace JoostBlog\WP\Comment\Inc;

/**
 * Manage links in comments.
 */
class Email_Links {

	/**
	 * Holds the plugins options.
	 */
	private array $options = [];

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = Hacks::get_options();

		\add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Init our hooks.
	 */
	public function init(): void {
		if ( \is_admin() ) {
			// Adds the email link to the actions on the comment overview page.
			\add_filter( 'comment_row_actions', [ $this, 'add_mailto_action_row' ] );

			return;
		}
		\add_action( 'admin_bar_menu', [ $this, 'admin_bar_comment_link' ], 65 );
		\add_action( 'wp_head', [ $this, 'wp_head_css' ] );
	}

	/**
	 * Adds an email link to the admin bar to email all commenters.
	 */
	public function admin_bar_comment_link(): void {
		if ( ! \is_singular() || $this->options['disable_email_all_commenters'] ) {
			return;
		}

		global $wp_admin_bar, $wpdb, $post;

		$current_user = \wp_get_current_user();

		$comments = \get_comments(
			[
				'post_id' => $post->ID,
				'type'    => 'comment',
				'status'  => 'approve',
			]
		);
		if ( count( $comments ) === 0 ) {
			return;
		}
		$emails = [];
		foreach ( $comments as $comment ) {
			$emails[] = $comment->comment_author_email;
		}
		$emails = \array_unique( $emails );

		$url = 'mailto:' . $current_user->user_email . '?bcc=';
		foreach ( $emails as $email ) {
			if ( $email !== $current_user->user_email ) {
				$url .= \rawurlencode( $email . ',' );
			}
		}
		$url .= '&subject=' . $this->replace_variables( $this->options['email_subject'], false, $post->ID );
		$url .= '&body=' . $this->replace_variables( $this->options['mass_email_body'], false, $post->ID );

		// We can't set the 'href' attribute to the $url as then esc_url would garble the mailto link.
		// So we do a nasty bit of JS workaround. The reason we grab the href from the alternate link is
		// so browser extensions like the Google Mail one that change mailto: links still work.
		echo '<a href="' . \esc_attr( $url ) . '" id="yst_email_commenters_alternate"></a><script>
			function yst_email_commenters(e){
				var ystEmailCommentersLink = document.getElementById( "yst_email_commenters_alternate" );
				e.preventDefault();
				if ( ystEmailCommentersLink === null ) {
					return;
				}
				window.location = ystEmailCommentersLink.getAttribute( "href" );
			}
		</script>';

		$wp_admin_bar->add_menu(
			[
				'id'    => 'yst-email-commenters',
				'title' => '<span class="ab-icon" title="' . \__( 'Email commenters', 'yoast-comment-hacks' ) . '"></span>',
				'href'  => '#',
				'meta'  => [ 'onclick' => 'yst_email_commenters(event)' ],
			]
		);
	}

	/**
	 * Adds styling to our email button.
	 */
	public function wp_head_css(): void {
		if ( ! \is_admin_bar_showing() ) {
			return;
		}

		echo '
		<style>
		#wpadminbar #wp-admin-bar-yst-email-commenters .ab-icon {
			width: 20px !important;
			height: 28px !important;
			padding: 6px 0 !important;
			margin-right: 0 !important;
		}
		#wpadminbar #wp-admin-bar-yst-email-commenters .ab-icon:before {
			content: "\f466";
		}
		</style>';
	}

	/**
	 * Adds an "E-Mail" action to the comment action list on the comments overview page.
	 *
	 * @param array $actions Array of actions we'll be adding our action to.
	 *
	 * @return array
	 */
	public function add_mailto_action_row( $actions ): array {
		/**
		 * The comment.
		 *
		 * @var $comment \WP_Comment
		 */
		global $comment;

		if ( $comment->comment_type !== 'comment' ) {
			return $actions;
		}

		$subject = $this->replace_variables( $this->options['email_subject'], $comment );
		$body    = $this->replace_variables( $this->options['email_body'], $comment );
		$link    = 'mailto:' . $comment->comment_author_email . '?subject=' . $subject . '&body=' . $body;

		$left_actions  = \array_slice( $actions, 0, 5 );
		$right_actions = \array_slice( $actions, 5 );

		$new_action = [
			/* translators: %s is replaced with the comment authors name */
			'mailto' => '<a href="' . \esc_attr( $link ) . '"><span class="dashicons dashicons-email-alt"></span> ' . \esc_html( \sprintf( \__( 'E-mail %s', 'yoast-comment-hacks' ), $comment->comment_author ) ) . '</a>',
		];

		return \array_merge( $left_actions, $new_action, $right_actions );
	}

	/**
	 * Replace variables with values in the message.
	 *
	 * @param string      $msg     The message in which we're replacing variables.
	 * @param bool|object $comment The comment object.
	 * @param int|bool    $post    The post the comment belongs to.
	 */
	private function replace_variables( $msg, $comment = false, $post = false ): string {
		$replacements = $this->get_replacements( $comment );

		if ( \is_numeric( $post ) ) {
			$post = \get_post( $post );
		}
		elseif ( \is_object( $comment ) && $comment->comment_post_ID > 0 ) {
			$post = \get_post( $comment->comment_post_ID );
		}

		if ( ! \is_object( $post ) ) {
			return $msg;
		}

		$replacements = \array_merge(
			$replacements,
			[
				'title'     => $post->post_title,
				'permalink' => \get_permalink( $post->ID ),
			]
		);

		foreach ( $replacements as $key => $value ) {
			$msg = \str_replace( '%' . $key . '%', $value, $msg );
		}

		return \rawurlencode( $msg );
	}

	/**
	 * Getting the replacements with comment data if there is a comment.
	 *
	 * @param bool|object $comment The comment object.
	 */
	private function get_replacements( $comment ): array {
		$replacements = [
			'email'     => '',
			'firstname' => '',
			'name'      => '',
			'url'       => '',
		];

		if ( \is_object( $comment ) ) {
			$name = \explode( ' ', $comment->comment_author );

			$replacements = [
				'email'     => $comment->comment_author_email,
				'firstname' => $name[0],
				'name'      => $comment->comment_author,
				'url'       => $comment->comment_author_url,
			];
		}

		return $replacements;
	}
}
