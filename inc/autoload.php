<?php

namespace EmiliaProjects\WP\Comment\Inc;

/**
 * Autoload the classes.
 */
class Autoload {

	/**
	 * Classmap. Key is the class name, value is the file path.
	 *
	 * @var string[]
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
		\spl_autoload_register( [ self::class, 'autoload' ] );
	}

	/**
	 * Autoload the classes.
	 *
	 * @param string $class_name The class name.
	 *
	 * @return void
	 */
	public static function autoload( string $class_name ): void {
		if ( ! isset( self::$classmap[ $class_name ] ) ) {
			return;
		}
		require_once \EMILIA_COMMENT_HACKS_PATH . self::$classmap[ $class_name ];
	}
}
