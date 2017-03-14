<?php
/**
 * @package YoastCommentHacks
 */

/**
 * Class YoastCommentNotifications
 *
 * @since 1.1
 */
class YoastCommentNotifications {

	/**
	 * YoastCommentNotifications constructor
	 */
	public function __construct() {
		add_filter( 'comment_notification_recipients', array( $this, 'filter_notification_recipients' ), 10, 2 );
		add_filter( 'comment_moderation_recipients', array( $this, 'filter_notification_recipients' ), 10, 2 );
		add_filter( 'comment_notification_headers', array( $this, 'filter_notification_headers' ), 10, 2 );
		add_filter( 'comment_moderation_headers', array( $this, 'filter_notification_headers' ), 10, 2 );
	}

	/**
	 * Filter the recipients of the comment notification
	 *
	 * @param array $recipients Recipients of the notification email.
	 * @param int   $comment_ID Comment the notification is sent for.
	 *
	 * @return array
	 */
	public function filter_notification_recipients( $recipients, $comment_ID ) {
		$comment = get_comment( $comment_ID );

		$new_recipient = get_post_meta( $comment->comment_post_ID, '_comment_notification_recipient', true );

		if ( ! empty( $new_recipient ) ) {
			$user = get_userdata( $new_recipient );

			return array( $user->user_email );
		}

		return $recipients;
	}

	/**
	 * Filter the headers of the comment notification
	 *
	 * @param string $message_headers
	 * @param int    $comment_id
	 *
	 * @return string
	 */
	public function filter_notification_headers( $message_headers, $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( '' !== $comment->comment_author && '' !== $comment->comment_author_email ) {
			$name = esc_html( $comment->comment_author );
			$message_headers .= "\nReply-To: $name <$comment->comment_author_email>\n";

			return $message_headers;
		}

		if ( '' !== $comment->comment_author_email ) {
			$message_headers .= "\nReply-To: $comment->comment_author_email <$comment->comment_author_email>\n";

			return $message_headers;
		}

		return $message_headers;
	}
}
