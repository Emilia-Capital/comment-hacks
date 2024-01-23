<?php

namespace EmiliaProjects\WP\Comment\Inc;

/**
 * Clean the emails.
 */
class Autoload {

	/**
	 * Classmap. Key is the class name, value is the file path.
	 *
	 * @var array
	 */
	private static array $classmap = [
		'EmiliaProjects\WP\Comment\Admin\Admin'          => 'admin/admin.php',
		'EmiliaProjects\WP\Comment\Admin\Comment_Parent' => 'admin/comment-parent.php',
		'EmiliaProjects\WP\Comment\Inc\Clean_Emails'     => 'inc/clean-emails.php',
		'EmiliaProjects\WP\Comment\Inc\Email_Links'      => 'inc/email-links.php',
		'EmiliaProjects\WP\Comment\Inc\Forms'            => 'inc/forms.php',
		'EmiliaProjects\WP\Comment\Inc\Hacks'            => 'inc/hacks.php',
		'EmiliaProjects\WP\Comment\Inc\Length'           => 'inc/length.php',
		'EmiliaProjects\WP\Comment\Inc\Notifications'    => 'inc/notifications.php',
	];

	/**
	 * Constructor.
	 *
	 * Register the autoloader.
	 */
	public function __construct() {
		\spl_autoload_register( [ __CLASS__, 'autoload' ] );
	}

	/**
	 * Autoload the classes.
	 *
	 * @param string $class_name The class name.
	 */
	public static function autoload( string $class_name ): void {
		if ( ! isset( static::$classmap[ $class_name ] ) ) {
			return;
		}
		require_once EMILIA_COMMENT_HACKS_PATH . static::$classmap[ $class_name ];
	}
}
