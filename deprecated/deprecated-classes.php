<?php
/**
 * Graceful deprecation of various classes which were renamed.
 *
 * @package YoastCommentHacks
 *
 * @since      1.6.0
 * @deprecated 1.6.0
 *
 * As this file is just (temporarily) put in place to warn extending plugins
 * about the class name changes, it is exempt from select CS standards.
 *
 * @phpcs:disable Yoast.Files.FileName.InvalidClassFileName
 * @phpcs:disable Yoast.Commenting.CodeCoverageIgnoreDeprecated
 * @phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound
 */

use Yoast\WP\Comment\Admin\Admin;
use Yoast\WP\Comment\Admin\Comment_Parent;
use Yoast\WP\Comment\Inc\Clean_Emails;
use Yoast\WP\Comment\Inc\Email_Links;

_deprecated_file( basename( __FILE__ ), 'Yoast Comment Hacks 1.6.0' );

/* ******************* /admin/ ******************* */

/**
 * Class YoastCommentHacksAdmin.
 *
 * @deprecated 1.6.0 Use {@see \Yoast\WP\Comment\Admin\Admin} instead.
 */
class YoastCommentHacksAdmin extends Admin {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \Yoast\WP\Comment\Admin\Admin} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Yoast Comment Hacks 1.6.0', '\Yoast\WP\Comment\Admin\Admin' );
		parent::__construct();
	}
}

/**
 * Class YoastCommentParent.
 *
 * @since      1.3
 * @deprecated 1.6.0 Use {@see \Yoast\WP\Comment\Admin\Comment_Parent} instead.
 */
class YoastCommentParent extends Comment_Parent {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \Yoast\WP\Comment\Admin\Comment_Parent} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Yoast Comment Hacks 1.6.0', '\Yoast\WP\Comment\Admin\Comment_Parent' );
		parent::__construct();
	}
}

/* ******************* /inc/ ******************* */

/**
 * Class YoastCleanEmails.
 *
 * @deprecated 1.6.0 Use {@see \Yoast\WP\Comment\Inc\Clean_Emails} instead.
 */
class YoastCleanEmails extends Clean_Emails {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \Yoast\WP\Comment\Inc\Clean_Emails} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Yoast Comment Hacks 1.6.0', '\Yoast\WP\Comment\Inc\Clean_Emails' );
		parent::__construct();
	}
}

/**
 * Class YoastCommentHacksEmailLinks.
 *
 * @deprecated 1.6.0 Use {@see \Yoast\WP\Comment\Inc\Email_Links} instead.
 */
class YoastCommentHacksEmailLinks extends Email_Links {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \Yoast\WP\Comment\Inc\Email_Links} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Yoast Comment Hacks 1.6.0', '\Yoast\WP\Comment\Inc\Email_Links' );
		parent::__construct();
	}
}
