<?php
/**
 * Comment Hacks admin file.
 *
 * @package YoastCommentHacks\Admin
 */

/**
 * Class YoastCommentHacksAdmin
 *
 * Renders and handles all admin activity for Comment Hacks.
 */
class Yoast_Comment_Hacks_Admin {
	/**
	 * The key that contains the comment notification recipient.
	 */
	const NOTIFICATION_RECIPIENT_KEY = '_comment_notification_recipient';
	/**
	 * The plugin page hook.
	 *
	 * @var string
	 */
	private $hook = 'yoast-comment-hacks';
	/**
	 * Holds the plugins options.
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->options = YoastCommentHacks::get_options();

		// Register the settings page.
		add_action( 'admin_menu', array( $this, 'add_config_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		// Register a link to the settings page on the plugins overview page.
		add_filter( 'plugin_action_links', array( $this, 'filter_plugin_actions' ), 10, 2 );

		// Filter the comment notification recipients.
		add_action( 'post_comment_status_meta_box-options', array( $this, 'reroute_comment_emails_option' ) );
		add_action( 'save_post', array( $this, 'save_reroute_comment_emails' ) );

		new Yoast_Comment_Parent();
	}

	/**
	 * Enqueue our admin script.
	 *
	 * @return void
	 */
	public function enqueue() {
		$page = filter_input( INPUT_GET, 'page' );

		if ( $page === 'yoast-comment-hacks' ) {
			$min = '.min';
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$min = '';
			}
			wp_enqueue_style( 'yoast-comment-hacks-admin-css', plugins_url( 'admin/assets/css/yoast-comment-hacks' . $min . '.css', YOAST_COMMENT_HACKS_FILE ), array(), YOAST_COMMENT_HACKS_VERSION );
			wp_enqueue_script( 'yoast-comment-hacks-admin-js', plugins_url( 'admin/assets/js/yoast-comment-hacks' . $min . '.js', YOAST_COMMENT_HACKS_FILE ), array(), YOAST_COMMENT_HACKS_VERSION );
		}
	}

	/**
	 * Register the promotion class for our GlotPress instance.
	 *
	 * @link https://github.com/Yoast/i18n-module
	 *
	 * @return void
	 */
	public function register_i18n_promo_class() {
		new Yoast_I18n_WordPressOrg_v3(
			array(
				'textdomain'  => 'yoast-comment-hacks',
				'plugin_name' => 'Yoast Comment Hacks',
				'hook'        => 'yoast_ch_admin_footer',
			)
		);
	}

	/**
	 * Adds the comment email recipients dropdown.
	 *
	 * @return void
	 */
	public function reroute_comment_emails_option() {
		echo '<br><br>';
		echo '<label for="comment_notification_recipient">' . esc_html__( 'Comment notification recipients:', 'yoast-comment-hacks' ) . '</label><br/>';

		$post_id = filter_input( INPUT_GET, 'post', FILTER_VALIDATE_INT );

		/**
		 * This filter allows filtering which roles should be shown in the dropdown for notifications. Defaults to contributor and up.
		 */
		$roles = apply_filters(
			'yoast_comment_hacks_notification_roles',
			array(
				'author',
				'contributor',
				'editor',
				'administrator',
			)
		);

		wp_dropdown_users(
			array(
				'selected'          => get_post_meta( $post_id, self::NOTIFICATION_RECIPIENT_KEY, true ),
				'show_option_none'  => 'Post author',
				'name'              => 'comment_notification_recipient',
				'id'                => 'comment_notification_recipient',
				'role__in'          => $roles,
				'option_none_value' => 0,
			)
		);
	}

	/**
	 * Saves the comment email recipients post meta.
	 *
	 * @return void
	 */
	public function save_reroute_comment_emails() {
		$post_id      = filter_input( INPUT_POST, 'ID', FILTER_VALIDATE_INT );
		$recipient_id = filter_input( INPUT_POST, 'comment_notification_recipient', FILTER_VALIDATE_INT );

		if ( $recipient_id && $post_id ) {
			update_post_meta( $post_id, self::NOTIFICATION_RECIPIENT_KEY, $recipient_id );
		}
	}

	/**
	 * Register the config page for all users that have the manage_options capability.
	 *
	 * @return void
	 */
	public function add_config_page() {
		add_options_page(
			__( 'Yoast Comment Hacks', 'yoast-comment-hacks' ),
			__( 'Comment Hacks', 'yoast-comment-hacks' ),
			'manage_options',
			$this->hook,
			array(
				$this,
				'config_page',
			)
		);
	}

	/**
	 * Register the settings link for the plugins page.
	 *
	 * @param array  $links The plugin action links.
	 * @param string $file  The plugin file.
	 *
	 * @return array Array with plugin settings links.
	 */
	public function filter_plugin_actions( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row. */
		static $this_plugin;
		if ( ! $this_plugin ) {
			$this_plugin = plugin_basename( __FILE__ );
		}

		if ( $file == $this_plugin ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=' . $this->hook ) . '">' . __( 'Settings', 'yoast-comment-hacks' ) . '</a>';
			// Put our link before other links.
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Output the config page.
	 *
	 * @since 0.5
	 *
	 * @return void
	 */
	public function config_page() {
		$this->register_i18n_promo_class();

		require_once 'views/config-page.php';

		// Show the content of the options array when debug is enabled.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			echo '<h4>Options debug</h4>';
			echo '<div style="border: 1px solid #aaa; padding: 20px;">';
			// @codingStandardsIgnoreStart
			echo str_replace( '<code>', '<code style="background-color: #eee; margin: 0; padding: 0;">', highlight_string( "<?php\n\$this->options = " . var_export( $this->options, true ) . ';', true ), $num );
			// @codingStandardsIgnoreEnd
			echo '</div>';
		}
	}
}
