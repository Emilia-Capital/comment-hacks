<?php

namespace JoostBlog\WP\Comment\Inc;

/**
 * Notifications about comments.
 */
class Notifications {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		\add_filter( 'comment_notification_recipients', [ $this, 'filter_notification_recipients' ], 10, 2 );
		\add_filter( 'comment_moderation_recipients', [ $this, 'filter_notification_recipients' ], 10, 2 );
		\add_filter( 'comment_notification_headers', [ $this, 'filter_notification_headers' ], 10, 2 );
		\add_filter( 'comment_moderation_headers', [ $this, 'filter_notification_headers' ], 10, 2 );
	}

	/**
	 * Filter the recipients of the comment notification.
	 *
	 * @param array $recipients Recipients of the notification email.
	 * @param int   $comment_id Comment the notification is sent for.
	 */
	public function filter_notification_recipients( $recipients, $comment_id ): array {
		$comment = \get_comment( $comment_id );

		$new_recipient = \get_post_meta( $comment->comment_post_ID, '_comment_notification_recipient', true );

		if ( ! empty( $new_recipient ) ) {
			$user = \get_userdata( $new_recipient );

			return [ $user->user_email ];
		}

		return $recipients;
	}

	/**
	 * Filter the headers of the comment notification.
	 *
	 * @param string $message_headers The headers of the message.
	 * @param int    $comment_id      The ID of the comment.
	 *
	 * @return string Enhanced headers.
	 */
	public function filter_notification_headers( $message_headers, $comment_id ): string {
		$comment = \get_comment( $comment_id );

		if ( $comment->comment_author !== '' && $comment->comment_author_email !== '' ) {
			$name             = \esc_html( $comment->comment_author );
			$message_headers .= "\nReply-To: $name <$comment->comment_author_email>\n";

			return $message_headers;
		}

		if ( $comment->comment_author_email !== '' ) {
			$message_headers .= "\nReply-To: $comment->comment_author_email <$comment->comment_author_email>\n";

			return $message_headers;
		}

		return $message_headers;
	}
}
