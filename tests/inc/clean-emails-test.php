<?php

namespace Yoast\WP\Comment\Tests\Inc;

use Yoast\WP\Comment\Inc\Clean_Emails;
use Yoast\WP\Comment\Tests\TestCase;

/**
 * Test class to test the Clean_Emails class.
 */
class Clean_Emails_Test extends TestCase {

	/**
	 * Tests class constructor.
	 *
	 * @covers \Yoast\WP\Comment\Inc\Clean_Emails::__construct
	 */
	public function test__construct() {
		$instance = new Clean_Emails();

		$this->assertSame(
			10,
			\has_filter( 'comment_notification_text', [ $instance, 'comment_notification_text' ] ),
			'Filter for the "comment_notification_text" not set or not at the correct priority'
		);
		$this->assertSame(
			10,
			\has_filter( 'comment_moderation_text', [ $instance, 'comment_moderation_text' ] ),
			'Filter for the "comment_moderation_text" not set or not at the correct priority'
		);
		$this->assertSame(
			10,
			\has_filter( 'comment_notification_headers', [ $instance, 'comment_email_headers' ] ),
			'Filter for the "comment_notification_headers" not set or not at the correct priority'
		);
		$this->assertSame(
			10,
			\has_filter( 'comment_moderation_headers', [ $instance, 'comment_email_headers' ] ),
			'Filter for the "comment_moderation_headers" not set or not at the correct priority'
		);
	}
}
