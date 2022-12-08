<?php
/**
 * Graceful deprecation of various classes which were renamed.
 *
 * As this file is just (temporarily) put in place to warn extending plugins
 * about the class name changes, it is exempt from select CS standards.
 *
 * @phpcs:disable Yoast.Files.FileName.InvalidClassFileName
 * @phpcs:disable Yoast.Commenting.CodeCoverageIgnoreDeprecated
 * @phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound
 */

use JoostBlog\WP\Comment\Admin\Admin;
use JoostBlog\WP\Comment\Admin\Comment_Parent;
use JoostBlog\WP\Comment\Inc\Clean_Emails;
use JoostBlog\WP\Comment\Inc\Email_Links;
use JoostBlog\WP\Comment\Inc\Forms;
use JoostBlog\WP\Comment\Inc\Hacks;
use JoostBlog\WP\Comment\Inc\Length;
use JoostBlog\WP\Comment\Inc\Notifications;

_deprecated_file( basename( __FILE__ ), 'Comment Hacks 1.6.0' );

/* ******************* /admin/ ******************* */

/**
 * Class YoastCommentHacksAdmin.
 *
 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Admin\Admin} instead.
 */
class YoastCommentHacksAdmin extends Admin {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Admin\Admin} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Comment Hacks 1.6.0', '\JoostBlog\WP\Comment\Admin\Admin' );
		parent::__construct();
	}
}

/**
 * Class YoastCommentParent.
 *
 * @since      1.3
 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Admin\Comment_Parent} instead.
 */
class YoastCommentParent extends Comment_Parent {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Admin\Comment_Parent} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Comment Hacks 1.6.0', '\JoostBlog\WP\Comment\Admin\Comment_Parent' );
		parent::__construct();
	}
}

/* ******************* /inc/ ******************* */

/**
 * Class YoastCleanEmails.
 *
 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Clean_Emails} instead.
 */
class YoastCleanEmails extends Clean_Emails {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Clean_Emails} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Comment Hacks 1.6.0', '\JoostBlog\WP\Comment\Inc\Clean_Emails' );
		parent::__construct();
	}
}

/**
 * Class YoastCommentHacksEmailLinks.
 *
 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Email_Links} instead.
 */
class YoastCommentHacksEmailLinks extends Email_Links {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Email_Links} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Comment Hacks 1.6.0', '\JoostBlog\WP\Comment\Inc\Email_Links' );
		parent::__construct();
	}
}

/**
 * Class YoastCommentFormHacks.
 *
 * @since      1.3
 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Forms} instead.
 */
class YoastCommentFormHacks extends Forms {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Forms} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Comment Hacks 1.6.0', '\JoostBlog\WP\Comment\Inc\Forms' );
		parent::__construct();
	}
}

/**
 * Class YoastCommentHacks.
 *
 * @since      1.0
 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Hacks} instead.
 */
class YoastCommentHacks extends Hacks {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Hacks} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Comment Hacks 1.6.0', '\JoostBlog\WP\Comment\Inc\Hacks' );
		parent::__construct();
	}
}

/**
 * Class YoastCommentLength.
 *
 * @since      1.3
 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Length} instead.
 */
class YoastCommentLength extends Length {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Length} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Comment Hacks 1.6.0', '\JoostBlog\WP\Comment\Inc\Length' );
		parent::__construct();
	}
}

/**
 * Class YoastCommentNotifications.
 *
 * @since      1.1
 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Notifications} instead.
 */
class YoastCommentNotifications extends Notifications {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see \JoostBlog\WP\Comment\Inc\Notifications} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Comment Hacks 1.6.0', '\JoostBlog\WP\Comment\Inc\Notifications' );
		parent::__construct();
	}
}
