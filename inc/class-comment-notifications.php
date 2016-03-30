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
	}

	/**
	 * Filter the recipients of the comment notification
	 *
	 * @param array $recipients
	 * @param int   $comment_ID
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
}
