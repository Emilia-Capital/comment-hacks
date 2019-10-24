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

_deprecated_file( basename( __FILE__ ), 'Yoast Comment Hacks 1.6.0' );

/* ******************* /admin/ ******************* */

/**
 * Class YoastCommentHacksAdmin.
 *
 * @deprecated 1.6.0 Use {@see Yoast_Comment_Admin} instead.
 */
class YoastCommentHacksAdmin extends Yoast_Comment_Admin {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see Yoast_Comment_Admin} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Yoast Comment Hacks 1.6.0', 'Yoast_Comment_Admin' );
		parent::__construct();
	}
}

/**
 * Class YoastCommentParent.
 *
 * @since      1.3
 * @deprecated 1.6.0 Use {@see Yoast_Comment_Parent} instead.
 */
class YoastCommentParent extends Yoast_Comment_Parent {

	/**
	 * Class constructor.
	 *
	 * @deprecated 1.6.0 Use {@see Yoast_Comment_Parent} instead.
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Yoast Comment Hacks 1.6.0', 'Yoast_Comment_Parent' );
		parent::__construct();
	}
}
