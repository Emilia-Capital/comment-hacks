<?php
/**
 * @package YoastCommentHacks\CleanEmails
 */

/**
 * Class YoastCleanEmails
 */
class YoastCleanEmails {

	/**
	 * @var int Holds the current comment's ID
	 */
	private $comment_id = 0;

	/**
	 * @var object Holds the current comment
	 */
	private $comment;

	/**
	 * @var object Holds the comment's post
	 */
	private $post;

	/**
	 * @var string Holds the email message
	 */
	private $message;

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter( 'comment_notification_text', array( $this, 'comment_notification_text' ), 10, 2 );
		add_filter( 'comment_moderation_text', array( $this, 'comment_moderation_text' ), 10, 2 );

		add_filter( 'comment_notification_headers', array( $this, 'comment_email_headers' ) );
		add_filter( 'comment_moderation_headers', array( $this, 'comment_email_headers' ) );
	}

	/**
	 * Set the comment email headers to HTML
	 *
	 * @param string $message_headers
	 *
	 * @return string $message_headers
	 */
	public function comment_email_headers( $message_headers ) {
		if ( '' === $message_headers ) {
			return 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\n";
		}

		return str_replace( 'Content-Type: text/plain', 'Content-Type: text/html', $message_headers );
	}

	/**
	 * Clean up the comment notification message
	 *
	 * @param string $message
	 * @param int    $comment_id
	 *
	 * @return string $message
	 */
	public function comment_notification_text( $message, $comment_id ) {
		$this->setup_data( $comment_id );

		$this->message = sprintf( __( 'New comment on "%s"', 'yoast-comment-hacks' ), '<a href="' . esc_url( get_permalink( $this->comment->comment_post_ID ) ) . '#comment-' . $comment_id . '">' . esc_html( $this->post->post_title ) . '</a>' ) . '<br /><br />';
		$this->add_comment_basics();

		if ( user_can( $this->post->post_author, 'edit_comment', $comment_id ) ) {
			$this->comment_notification_actions();
		}

		return $this->wrap_message();
	}

	/**
	 * Clean up the comment moderation message
	 *
	 * @param string $message
	 * @param int    $comment_id
	 *
	 * @return string $message
	 */
	public function comment_moderation_text( $message, $comment_id ) {
		$this->setup_data( $comment_id );

		$this->message = sprintf( __( 'A new %1$s on the post "%2$s" is waiting for your approval:', 'yoast-comment-hacks' ), $this->comment->comment_type, '<a href="' . get_permalink( $this->comment->comment_post_ID ) . '">' . esc_html( $this->post->post_title ) . '</a>' ) . '<br /><br />';
		$this->add_comment_basics();

		$this->comment_moderation_actions();
		$this->message .= ' | ' . sprintf( '<a href="http://whois.arin.net/rest/ip/%1$s">%2$s</a>', $this->comment->comment_author_IP, __( 'Whois', 'yoast-comment-hacks' ) );
		$this->message .= '<br/><br/>';

		$this->get_moderation_msg();

		return $this->wrap_message( $this->message );
	}

	/**
	 * Adds the basics of the email
	 */
	private function add_comment_basics() {
		$this->add_author_line();
		$this->add_url_line();
		$this->message .= '<br/>';
		$this->add_content_line();
	}

	/**
	 * Adds the author line to the message
	 */
	private function add_author_line() {
		if ( '' === $this->comment->comment_type ) {
			$this->message .= sprintf( __( 'Author: %1$s (%2$s)', 'yoast-comment-hacks' ), esc_html( $this->comment->comment_author ), '<a href="' . esc_url( 'mailto:' . $this->comment->comment_author_email ) . '">' . esc_html( $this->comment->comment_author_email ) . '</a>' ) . '<br />';
		}
		else {
			$this->message .= sprintf( __( 'Website: %1$s', 'yoast-comment-hacks' ), esc_html( $this->comment->comment_author ) ) . '<br>';
		}
	}

	/**
	 * Adds the content line to the message
	 */
	private function add_content_line() {
		if ( '' === $this->comment->comment_type ) {
			$this->message .= __( 'Comment:', 'yoast-comment-hacks' ) . '<br />' . wpautop( $this->comment->comment_content ) . '<br />';
		}
		else {
			$this->message .= __( 'Excerpt:', 'yoast-comment-hacks' ) . '<br /> [...] ' . wpautop( $this->comment->comment_content ) . ' [...] <br />';
		}
	}

	/**
	 * Adds the URL line to the message
	 */
	private function add_url_line() {
		if ( isset( $this->comment->comment_author_url ) && '' !== $this->comment->comment_author_url ) {
			$this->message .= sprintf( __( 'URL: %s', 'yoast-comment-hacks' ), '<a href="' . esc_url( $this->comment->comment_author_url ) . '">' . esc_html( $this->comment->comment_author_url ) . '</a>' ) . '<br/>';
		}
	}

	/**
	 * Wraps the message in some styling
	 *
	 * @return string
	 */
	private function wrap_message() {
		return '<div style="font-family:Helvetica,Arial,sans-serif; font-size:14px;">' . $this->message . '</div>';
	}

	/**
	 * Sets up class variables used with all emails
	 *
	 * @param int $comment_id
	 */
	private function setup_data( $comment_id ) {
		$this->comment_id = $comment_id;
		$this->comment    = get_comment( $this->comment_id );
		$this->post       = get_post( $this->comment->comment_post_ID );

		if ( 'comment' === $this->comment->comment_type ) {
			$this->comment->comment_type = '';
		}
	}

	/**
	 * Adds a sentence about the number of comments awaiting moderation
	 */
	private function get_moderation_msg() {
		global $wpdb;
		$comments_waiting = $wpdb->get_var( "SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'" );

		if ( $comments_waiting > 1 ) {
			$comments_waiting--;
			$this->message .= sprintf( __( 'Currently this and %s other comments are waiting for approval.', 'yoast-comment-hacks' ), number_format_i18n( $comments_waiting ) );
			$this->message .= ' ';
			$this->message .= sprintf( __( 'Please visit the %1$smoderation panel%2$s.', 'yoast-comment-hacks' ), '<a href="' . admin_url( 'edit-comments.php?comment_status=moderated' ) . '">', '</a>' ) . '<br>';
		}
	}

	/**
	 * Returns a string containing comment moderation links
	 */
	private function comment_moderation_actions() {
		$actions = array(
			'approve'     => __( 'Approve', 'yoast-comment-hacks' ),
			'spam'        => __( 'Spam', 'yoast-comment-hacks' ),
			'trash'       => __( 'Trash', 'yoast-comment-hacks' ),
			'editcomment' => __( 'Edit', 'yoast-comment-hacks' ),
		);

		$this->comment_action_links( $actions );
	}

	/**
	 * Returns a string containing comment action links
	 */
	private function comment_notification_actions() {
		$actions = array(
			'spam'        => __( 'Spam', 'yoast-comment-hacks' ),
			'trash'       => __( 'Trash', 'yoast-comment-hacks' ),
			'editcomment' => __( 'Edit', 'yoast-comment-hacks' ),
		);

		$this->comment_action_links( $actions );
	}

	/**
	 * Add action links to the message
	 *
	 * @param array $actions
	 */
	private function comment_action_links( $actions ) {
		$links = '';
		foreach ( $actions as $action => $label ) {
			$links .= $this->comment_action_link( $label, $action ) . ' | ';
		}

		$links = rtrim( $links, '| ' );

		$this->message .= $links;
	}

	/**
	 * Creates a comment action link
	 *
	 * @param string $label
	 * @param string $action
	 *
	 * @return string
	 */
	private function comment_action_link( $label, $action ) {
		$url = admin_url( sprintf( 'comment.php?action=%s&c=%d', $action, $this->comment_id ) );

		return sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( $label ) );
	}
}