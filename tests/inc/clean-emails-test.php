<?php

namespace EmiliaProjects\WP\Comment\Tests\Inc;

use stdClass;
use EmiliaProjects\WP\Comment\Inc\Clean_Emails;
use EmiliaProjects\WP\Comment\Tests\TestCase;

/**
 * Test class to test the Clean_Emails class.
 */
class Clean_Emails_Test extends TestCase {

	/**
	 * Tests class constructor.
	 *
	 * @covers \EmiliaProjects\WP\Comment\Inc\Clean_Emails::__construct
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

	/**
	 * Test setting the content type header for comment emails to "text/html".
	 *
	 * @dataProvider data_comment_email_headers
	 * @covers       \EmiliaProjects\WP\Comment\Inc\Clean_Emails::comment_email_headers
	 *
	 * @param mixed  $headers  The initial message headers.
	 * @param string $expected The expected function return value.
	 *
	 * @return void
	 */
	public function test_comment_email_headers( $headers, $expected ) {
		$instance = new Clean_Emails();
		$this->assertSame( $expected, $instance->comment_email_headers( $headers ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function data_comment_email_headers() {
		$object = new stdClass();

		return [
			// Ensure the header is added when it is missing.
			'empty header string' => [
				'headers'  => '',
				'expected' => "Content-Type: text/html; charset=\"UTF-8\"\n",
			],
			'header string with headers, but without content type header' => [
				'headers'  => 'From: "Blogname" <blogname@blogdomain.com>
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
				'expected' => 'From: "Blogname" <blogname@blogdomain.com>
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
Content-Type: text/html; charset="UTF-8"
',
			],
			'header string with headers, but without content type header and missing new line at end of headers' => [
				'headers'  => 'From: "Blogname" <blogname@blogdomain.com>
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>',
				'expected' => 'From: "Blogname" <blogname@blogdomain.com>
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
Content-Type: text/html; charset="UTF-8"
',
			],

			// Ensure the header is changed to "text/html" when it exists and is set to "text/plain".
			'header string consisting of only a "text/plain" content type header' => [
				'headers'  => 'Content-Type: text/plain; charset="UTF-8"',
				'expected' => 'Content-Type: text/html; charset="UTF-8"',
			],
			'header string containing a "text/plain" content type header and other headers' => [
				'headers'  => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: text/plain; charset="UTF-8"
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
				'expected' => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: text/html; charset="UTF-8"
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
			],

			// Ensure that a header which is already correct is returned without changes.
			'header string consisting of only a "text/html" content type header' => [
				'headers'  => 'Content-Type: text/html; charset="UTF-8"',
				'expected' => 'Content-Type: text/html; charset="UTF-8"',
			],

			/*
			 * Ensure that if there is a content-type header, but it's not text/plain, the header is
			 * returned without changes.
			 * Unexpected headers like this could happen because another plugin does something spiffy
			 * with the comment email and we should try to avoid breaking their integration.
			 */
			'header string containing a non-standard "text" content type header and other headers' => [
				'headers'  => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: text/raw; charset="UTF-8"
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
				'expected' => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: text/raw; charset="UTF-8"
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
			],
			'header string containing a non-"text" content type header and other headers' => [
				'headers'  => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: message/partial;
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
				'expected' => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: message/partial;
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
			],

			/*
			 * The `comment_notification_headers` filter $message_header parameter is **always** supposed to be
			 * a string.
			 * If a non-string value is received, another plugin hooked in before us is _doing_it_wrong_.
			 * For null and scalar input, the value should be corrected.
			 * For non-scalar input, we leave things as they are to avoid breaking the other plugins integration
			 * (and to let the onus of things going wrong land on them).
			 */
			'invalid input type: null' => [
				'headers'  => null,
				'expected' => "Content-Type: text/html; charset=\"UTF-8\"\n",
			],
			'invalid input type: false' => [
				'headers'  => false,
				'expected' => "Content-Type: text/html; charset=\"UTF-8\"\n",
			],
			'invalid input type: true' => [
				'headers'  => true,
				'expected' => "Content-Type: text/html; charset=\"UTF-8\"\n",
			],
			'invalid input type: integer' => [
				'headers'  => 105,
				'expected' => "Content-Type: text/html; charset=\"UTF-8\"\n",
			],
			'invalid input type: array' => [
				'headers'  => [
					'From: "Blogname" <blogname@blogdomain.com>',
					'Content-Type: text/plain; charset="UTF-8"',
					'Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>',
				],
				'expected' => [
					'From: "Blogname" <blogname@blogdomain.com>',
					'Content-Type: text/plain; charset="UTF-8"',
					'Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>',
				],
			],
			'invalid input type: object' => [
				'headers'  => $object,
				'expected' => $object,
			],
		];
	}
}
