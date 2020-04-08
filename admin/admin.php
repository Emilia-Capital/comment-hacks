<?php

namespace Yoast\WP\Comment\Admin;

use Yoast\WP\Comment\Inc\Hacks;
use Yoast_I18n_WordPressOrg_v3;

/**
 * Admin handling class.
 *
 * @since 1.6.0 Class renamed from `YoastCommentHacksAdmin` to `Yoast\WP\Comment\Admin\Admin`.
 */
class Admin {

	/**
	 * Recipient key.
	 *
	 * @var string
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
	private $options = [];

	/**
	 * The absolute minimum comment length when this plugin is enabled.
	 *
	 * @var int
	 */
	private $absolute_min = 0;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = Hacks::get_options();

		// Hook into init for registration of the option and the language files.
		\add_action( 'admin_init', [ $this, 'init' ] );

		// Register the settings page.
		\add_action( 'admin_menu', [ $this, 'add_config_page' ] );
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );

		// Register a link to the settings page on the plugins overview page.
		\add_filter( 'plugin_action_links', [ $this, 'filter_plugin_actions' ], 10, 2 );

		// Filter the comment notification recipients.
		\add_action( 'post_comment_status_meta_box-options', [ $this, 'reroute_comment_emails_option' ] );
		\add_action( 'save_post', [ $this, 'save_reroute_comment_emails' ] );

		new Comment_Parent();
	}

	/**
	 * Register the text domain and the options array along with the validation function.
	 */
	public function init() {
		// Register our option array.
		\register_setting(
			Hacks::$option_name,
			Hacks::$option_name,
			[
				$this,
				'options_validate',
			]
		);
	}

	/**
	 * Enqueue our admin script.
	 */
	public function enqueue() {
		$page = \filter_input( \INPUT_GET, 'page' );

		if ( $page === 'yoast-comment-hacks' ) {
			$min = '.min';
			if ( \defined( 'SCRIPT_DEBUG' ) && \SCRIPT_DEBUG ) {
				$min = '';
			}

			\wp_enqueue_style(
				'yoast-comment-hacks-admin-css',
				\plugins_url( 'admin/assets/css/yoast-comment-hacks.css', \YOAST_COMMENT_HACKS_FILE ),
				[],
				\YOAST_COMMENT_HACKS_VERSION
			);

			\wp_enqueue_script(
				'yoast-comment-hacks-admin-js',
				\plugins_url( 'admin/assets/js/yoast-comment-hacks' . $min . '.js', \YOAST_COMMENT_HACKS_FILE ),
				[],
				\YOAST_COMMENT_HACKS_VERSION,
				true
			);
		}
	}

	/**
	 * Register the promotion class for our GlotPress instance.
	 *
	 * @link https://github.com/Yoast/i18n-module
	 */
	public function register_i18n_promo_class() {
		new Yoast_I18n_WordPressOrg_v3(
			[
				'textdomain'  => 'yoast-comment-hacks',
				'plugin_name' => 'Yoast Comment Hacks',
				'hook'        => 'Yoast\WP\Comment\admin_footer',
			]
		);
	}

	/**
	 * Adds the comment email recipients dropdown.
	 */
	public function reroute_comment_emails_option() {
		echo '<br><br>';
		echo '<label for="comment_notification_recipient">' . \esc_html__( 'Comment notification recipients:', 'yoast-comment-hacks' ) . '</label><br/>';

		$post_id = \filter_input( \INPUT_GET, 'post', \FILTER_VALIDATE_INT );

		/**
		 * This filter allows filtering which roles should be shown in the dropdown for notifications.
		 * Defaults to contributor and up.
		 *
		 * @deprecated 1.6.0. Use the {@see 'Yoast\WP\Comment\notification_roles'} filter instead.
		 *
		 * @param array $roles Array with user roles.
		 */
		$roles = \apply_filters_deprecated(
			'yoast_comment_hacks_notification_roles',
			[
				[
					'author',
					'contributor',
					'editor',
					'administrator',
				],
			],
			'Yoast Comment 1.6.0',
			'Yoast\WP\Comment\notification_roles'
		);

		/**
		 * This filter allows filtering which roles should be shown in the dropdown for notifications.
		 * Defaults to contributor and up.
		 *
		 * @since 1.6.0
		 *
		 * @param array $roles Array with user roles.
		 */
		$roles = \apply_filters( 'Yoast\WP\Comment\notification_roles', $roles );

		\wp_dropdown_users(
			[
				'selected'          => \get_post_meta( $post_id, self::NOTIFICATION_RECIPIENT_KEY, true ),
				'show_option_none'  => 'Post author',
				'name'              => 'comment_notification_recipient',
				'id'                => 'comment_notification_recipient',
				'role__in'          => $roles,
				'option_none_value' => 0,
			]
		);
	}

	/**
	 * Saves the comment email recipients post meta.
	 */
	public function save_reroute_comment_emails() {

		$post_id      = \filter_input( \INPUT_POST, 'ID', \FILTER_VALIDATE_INT );
		$recipient_id = \filter_input( \INPUT_POST, 'comment_notification_recipient', \FILTER_VALIDATE_INT );

		if ( $recipient_id && $post_id ) {
			\update_post_meta( $post_id, self::NOTIFICATION_RECIPIENT_KEY, $recipient_id );
		}
	}

	/**
	 * Validate the input, make sure comment length is an integer and above the minimum value.
	 *
	 * @since 1.0
	 *
	 * @param array $input Input with unvalidated options.
	 *
	 * @return array Validated input.
	 */
	public function options_validate( $input ) {
		$defaults = Hacks::get_defaults();

		$input['mincomlength']  = (int) $input['mincomlength'];
		$input['maxcomlength']  = (int) $input['maxcomlength'];
		$input['redirect_page'] = (int) $input['redirect_page'];
		$input['clean_emails']  = isset( $input['clean_emails'] ) ? 1 : 0;
		$input['version']       = \YOAST_COMMENT_HACKS_VERSION;

		foreach ( [ 'email_subject', 'email_body', 'mass_email_body' ] as $key ) {
			if ( $input[ $key ] === '' ) {
				$input[ $key ] = $defaults[ $key ];
			}
		}

		if ( ( $this->absolute_min + 1 ) > $input['mincomlength'] || empty( $input['mincomlength'] ) ) {
			/* translators: %d is replaced with the minimum number of characters */
			\add_settings_error( $this->option_name, 'min_length_invalid', \sprintf( \__( 'The minimum length you entered is invalid, please enter a minimum length above %d.', 'yoast-comment-hacks' ), $this->absolute_min ) );
			$input['mincomlength'] = 15;
		}

		return $input;
	}

	/**
	 * Register the config page for all users that have the manage_options capability.
	 */
	public function add_config_page() {
		\add_options_page(
			\__( 'Yoast Comment Hacks', 'yoast-comment-hacks' ),
			\__( 'Comment Hacks', 'yoast-comment-hacks' ),
			'manage_options',
			$this->hook,
			[
				$this,
				'config_page',
			]
		);
	}

	/**
	 * Register the settings link for the plugins page.
	 *
	 * @param array  $links The plugin action links.
	 * @param string $file  The plugin file.
	 *
	 * @return array
	 */
	public function filter_plugin_actions( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row. */
		static $this_plugin;
		if ( ! $this_plugin ) {
			$this_plugin = \plugin_basename( __FILE__ );
		}

		if ( $file === $this_plugin ) {
			$settings_link = '<a href="' . \admin_url( 'options-general.php?page=' . $this->hook ) . '">' . \__( 'Settings', 'yoast-comment-hacks' ) . '</a>';
			// Put our link before other links.
			\array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Output the config page.
	 *
	 * @since 0.5
	 */
	public function config_page() {
		$this->register_i18n_promo_class();

		require_once \YST_COMMENT_HACKS_PATH . 'admin/views/config-page.php';

		// Show the content of the options array when debug is enabled.
		if ( \defined( 'WP_DEBUG' ) && \WP_DEBUG ) {
			echo '<h4>', \esc_html__( 'Options debug', 'yoast-comment-hacks' ), '</h4>';
			echo '<div style="border: 1px solid #aaa; padding: 20px;">';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Debug output.
			echo \str_replace(
				'<code>',
				'<code style="background-color: #eee; margin: 0; padding: 0;">',
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export -- This is only shown in debug mode.
				\highlight_string( "<?php\n\$this->options = " . \var_export( $this->options, true ) . ';', true ),
				$num
			);
			echo '</div>';
		}
	}
}
