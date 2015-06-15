<?php
/**
 * @package YoastCommentHacks\Admin
 */

/**
 * Class YoastCommentHacksAdmin
 */
class YoastCommentHacksAdmin {

	/**
	 * @var string The plugin page hook
	 */
	private $hook = 'yoast-comment-hacks';

	/**
	 * @var string Holds the plugins option name
	 */
	private $option_name = 'yoast_comment_hacks';

	/**
	 * @var array Holds the plugins options
	 */
	private $options = array();

	/**
	 * @var int The absolute minimum comment length when this plugin is enabled
	 */
	private $absolute_min = 0;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->options = get_option( $this->option_name );

		// Hook into init for registration of the option and the language files
		add_action( 'admin_init', array( $this, 'init' ) );

		// Register the settings page
		add_action( 'admin_menu', array( $this, 'add_config_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		// The hooks for editing and saving the comment parent
		add_action( 'admin_menu', array( $this, 'load_comment_parent_box' ) );
		add_action( 'edit_comment', array( $this, 'update_comment_parent' ) );

		// Register a link to the settings page on the plugins overview page
		add_filter( 'plugin_action_links', array( $this, 'filter_plugin_actions' ), 10, 2 );
	}

	/**
	 * Register the text domain and the options array along with the validation function
	 */
	public function init() {
		// Allow for localization
		load_plugin_textdomain( 'yoast-comment-hacks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		// Register our option array
		register_setting( $this->option_name, $this->option_name, array( $this, 'options_validate' ) );
	}

	/**
	 * Enqueue our admin script
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
	 * Register the promotion class for our GlotPress instance
	 *
	 * @link https://github.com/Yoast/i18n-module
	 */
	public function register_i18n_promo_class() {
		new yoast_i18n(
			array(
				'textdomain'     => 'yoast-comment-hacks',
				'project_slug'   => 'comment-hacks',
				'plugin_name'    => 'Yoast Comment Hacks',
				'hook'           => 'yoast_ch_admin_footer',
				'glotpress_url'  => 'http://translate.yoast.com/',
				'glotpress_name' => 'Yoast Translate',
				'glotpress_logo' => 'https://cdn.yoast.com/wp-content/uploads/i18n-images/Yoast_Translate.svg',
				'register_url'   => 'http://translate.yoast.com/projects#utm_source=plugin&utm_medium=promo-box&utm_campaign=wpseo-i18n-promo',
			)
		);
	}

	/**
	 * Shows the comment parent box where you can change the comment parent
	 *
	 * @param object $comment
	 */
	public function comment_parent_box( $comment ) {
		require_once 'views/comment-parent-box.php';
	}

	/**
	 * Adds the comment parent box to the meta box
	 */
	public function load_comment_parent_box() {
		if ( function_exists( 'add_meta_box' ) ) {
			add_meta_box( 'comment_parent', 'Comment Parent', array(
				$this,
				'comment_parent_box',
			), 'comment', 'normal' );
		}
	}

	/**
	 * Updates the comment parent field
	 */
	public function update_comment_parent() {
		$comment_parent = filter_input( INPUT_POST, 'yst_comment_parent', FILTER_VALIDATE_INT );
		$comment_id     = filter_input( INPUT_POST, 'comment_ID', FILTER_VALIDATE_INT );

		check_admin_referer( 'update-comment_' . $comment_id );

		if ( $comment_parent ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->comments SET comment_parent = %d WHERE comment_ID = %d", $comment_parent, $comment_id ) );
		}
	}

	/**
	 * Validate the input, make sure comment length is an integer and above the minimum value.
	 *
	 * @since 1.0
	 *
	 * @param array $input with unvalidated options.
	 *
	 * @return array $input with validated options.
	 */
	public function options_validate( $input ) {
		$defaults = YoastCommentHacks::get_defaults();

		$input['mincomlength']  = (int) $input['mincomlength'];
		$input['redirect_page'] = (int) $input['redirect_page'];
		$input['clean_emails']  = isset( $input['clean_emails'] );

		foreach ( array( 'email_subject', 'email_body', 'mass_email_body' ) as $key ) {
			if ( '' === $input[ $key ] ) {
				$input[ $key ] = $defaults[ $key ];
			}
		}

		if ( ( $this->absolute_min + 1 ) > $input['mincomlength'] || empty( $input['mincomlength'] ) ) {
			add_settings_error( $this->option_name, 'min_length_invalid', sprintf( __( 'The minimum length you entered is invalid, please enter a minimum length above %d.', 'yoast-comment-hacks' ), $this->absolute_min ) );
			$input['mincomlength'] = 15;
		}

		return $input;
	}

	/**
	 * Register the config page for all users that have the manage_options capability
	 */
	public function add_config_page() {
		add_options_page( __( 'Yoast Comment Hacks', 'yoast-comment-hacks' ), __( 'Comment Hacks', 'yoast-comment-hacks' ), 'manage_options', $this->hook, array(
			$this,
			'config_page',
		) );
	}

	/**
	 * Register the settings link for the plugins page
	 *
	 * @param array  $links
	 * @param string $file
	 *
	 * @return array
	 */
	public function filter_plugin_actions( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row. */
		static $this_plugin;
		if ( ! $this_plugin ) {
			$this_plugin = plugin_basename( __FILE__ );
		}

		if ( $file == $this_plugin ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=' . $this->hook ) . '">' . __( 'Settings', 'yoast-comment-hacks' ) . '</a>';
			array_unshift( $links, $settings_link ); // before other links
		}

		return $links;
	}

	/**
	 * Output the config page
	 *
	 * @since 0.5
	 */
	public function config_page() {
		$this->register_i18n_promo_class();

		require_once 'views/config-page.php';

		// Show the content of the options array when debug is enabled
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			echo '<h4>Options debug</h4>';
			echo '<pre style="background-color: white; border: 1px solid #aaa; padding: 20px;">';
			// @codingStandardsIgnoreStart
			var_dump( $this->options );
			// @codingStandardsIgnoreEnd
			echo '</pre>';
		}
	}

}
