<?php

namespace JoostBlog\WP\Comment\Admin;

use WP_Comment;
use WP_Post;
use JoostBlog\WP\Comment\Inc\Hacks;

/**
 * Admin handling class.
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
	 */
	private string $hook = 'yoast-comment-hacks';

	/**
	 * Holds the plugins options.
	 */
	private array $options = [];

	/**
	 * The absolute minimum comment length when this plugin is enabled.
	 */
	private int $absolute_min = 0;

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
		\add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
		\add_action( 'pre_post_update', [ $this, 'save_reroute_comment_emails' ] );

		\add_filter( 'comment_row_actions', [ $this, 'forward_to_support_action_link' ], 10, 2 );
		\add_action( 'admin_head', [ $this, 'forward_comment' ] );
		\add_filter( 'comment_text', [ $this, 'show_forward_status' ], 10, 2 );

		new Comment_Parent();
	}

	/**
	 * Show when a comment was forwarded already.
	 *
	 * @param string     $comment_text Text of the current comment.
	 * @param WP_Comment $comment      The comment object. Null if not found.
	 */
	public function show_forward_status( $comment_text, $comment ): string {
		if ( ! \is_admin() ) {
			return $comment_text;
		}

		$ch_forwarded = \get_comment_meta( $comment->comment_ID, 'ch_forwarded' );
		if ( $ch_forwarded ) {
			/* translators: %s is replaced by the name you're forwarding to. */
			$pre          = '<div style="background: #fff;border: 1px solid #46b450;border-left-width: 4px;box-shadow: 0 1px 1px rgba(0,0,0,.04);margin: 5px 15px 2px 0;padding: 1px 12px 1px;"><p><strong>' . \sprintf( \esc_html__( 'This comment was forwarded to %s.', 'yoast-comment-hacks' ), \esc_html( $this->options['forward_name'] ) ) . '</strong></p></div>';
			$comment_text = $pre . $comment_text;
		}

		return $comment_text;
	}

	/**
	 * Forwards a comment to an email address chosen in the settings.
	 */
	public function forward_comment(): void {
		if ( empty( $this->options['forward_email'] ) ) {
			return;
		}

		if (
			isset( $_GET['ch_action'] )
			&& isset( $_GET['nonce'] )
			&& isset( $_GET['comment_id'] )
			&& $_GET['ch_action'] === 'forward_comment'
			&& \wp_verify_nonce( \wp_strip_all_tags( \wp_unslash( $_GET['nonce'] ) ), 'comment-hacks-forward' )
		) {
			$comment_id = (int) $_GET['comment_id'];
			$comment    = \get_comment( $comment_id );

			/* translators: %1$s is replaced by (a link to) the blog's name, %2$s by (a link to) the title of the blogpost. */
			echo '<div class="msg updated"><p>' . \sprintf( \esc_html__( 'Forwarding comment from %1$s to %2$s.', 'yoast-comment-hacks' ), '<strong>' . \esc_html( $comment->comment_author ) . '</strong>', \esc_html( $this->options['forward_name'] ) ) . '</div></div>';

			$intro = \sprintf( 'This comment was forwarded from %s where it was left on: %s.', '<a href=" ' . \get_site_url() . ' ">' . \esc_html( \get_bloginfo( 'name' ) ) . '</a>', '<a href="' . \get_permalink( $comment->comment_post_ID ) . '">' . \get_the_title( $comment->comment_post_ID ) . '</a>' ) . "\n\n";

			if ( ! empty( $this->options['forward_extra'] ) ) {
				$intro .= $this->options['forward_extra'] . "\n\n";
			}

			$intro .= '---------- Forwarded message ---------
From: ' . \esc_html( $comment->comment_author ) . ' &lt;' . \esc_html( $comment->comment_author_email ) . '&gt;
Date: ' . \gmdate( 'D, M j, Y \a\t h:i A', \strtotime( $comment->comment_date ) ) . '
Subject: ' . \esc_html__( 'Comment on', 'yoast-comment-hacks' ) . ' ' . \esc_html( \get_bloginfo( 'name' ) ) . '
To: ' . \esc_html( \get_bloginfo( 'name' ) ) . ' &lt;' . \esc_html( $this->options['forward_from_email'] ) . '&gt;';
			$intro .= "\n\n";

			$content = \nl2br( $intro . $comment->comment_content );

			$headers = [
				'From: ' . \get_bloginfo( 'name' ) . ' <' . \esc_html( $this->options['forward_from_email'] ) . '>',
				'Content-Type: text/html; charset=UTF-8',
			];
			\wp_mail( $this->options['forward_email'], $this->options['forward_subject'], $content, $headers );

			// Don't send an already approved comment to the trash.
			if ( ! $comment->comment_approved ) {
				\update_comment_meta( $comment_id, 'ch_forwarded', true );
				\wp_set_comment_status( $comment_id, 'trash' );
			}
		}
	}

	/**
	 * Adds an action link to forward a comment to your support team.
	 *
	 * @param string[]   $actions The actions shown underneath comments.
	 * @param WP_Comment $comment The individual comment object.
	 */
	public function forward_to_support_action_link( $actions, $comment ): array {
		if ( empty( $this->options['forward_email'] ) ) {
			return $actions;
		}

		$label = \esc_html__( 'Forward to support', 'yoast-comment-hacks' );

		// '1' === approved, 'trash' === trashed.
		if ( $comment->comment_approved !== '1' && $comment->comment_approved !== 'trash' ) {
			$label = \esc_html__( 'Forward to support & trash', 'yoast-comment-hacks' );
		}

		$actions['ch_forward'] = '<a href="' . \admin_url( 'edit-comments.php' ) . '?comment_id=' . $comment->comment_ID . '&ch_action=forward_comment&nonce=' . \wp_create_nonce( 'comment-hacks-forward' ) . '">' . $label . '</a>';

		return $actions;
	}

	/**
	 * Register meta box(es).
	 */
	public function register_meta_boxes(): void {
		\add_meta_box(
			'comment-hacks-reroute',
			\__( 'Comment Hacks', 'yoast-comment-hacks' ),
			[
				$this,
				'meta_box_callback',
			],
			'post',
			'side'
		);
	}

	/**
	 * Meta box display callback.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function meta_box_callback( $post ): void {
		echo '<input type="hidden" name="comment_notification_recipient_nonce" value="' . \esc_attr( \wp_create_nonce( 'comment_notification_recipient_nonce' ) ) . '" />';
		echo '<label for="comment_notification_recipient">' . \esc_html__( 'Comment notification recipients:', 'yoast-comment-hacks' ) . '</label><br/>';

		/**
		 * This filter allows filtering which roles should be shown in the dropdown for notifications.
		 * Defaults to contributor and up.
		 *
		 * @param array $roles Array with user roles.
		 *
		 * @deprecated 1.6.0. Use the {@see 'JoostBlog\WP\Comment\notification_roles'} filter instead.
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
			'Comment Hacks 1.6.0',
			'JoostBlog\WP\Comment\notification_roles'
		);

		/**
		 * This filter allows filtering which roles should be shown in the dropdown for notifications.
		 * Defaults to contributor and up.
		 *
		 * @param array $roles Array with user roles.
		 *
		 * @since 1.6.0
		 */
		$roles = \apply_filters( 'JoostBlog\WP\Comment\notification_roles', $roles );

		\wp_dropdown_users(
			[
				'selected'          => \get_post_meta( $post->ID, self::NOTIFICATION_RECIPIENT_KEY, true ),
				'show_option_none'  => 'Post author',
				'name'              => 'comment_notification_recipient',
				'id'                => 'comment_notification_recipient',
				'role__in'          => $roles,
				'option_none_value' => 0,
			]
		);
	}

	/**
	 * Register the options array along with the validation function.
	 */
	public function init(): void {
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
	public function enqueue(): void {
		$page = \filter_input( \INPUT_GET, 'page' );

		if ( $page === 'yoast-comment-hacks' ) {
			$min = '.min';
			if ( \defined( 'SCRIPT_DEBUG' ) && \SCRIPT_DEBUG ) {
				$min = '';
			}

			\wp_enqueue_style(
				'yoast-comment-hacks-admin-css',
				\plugins_url( 'admin/assets/css/dist/comment-hacks.css', \JOOST_COMMENT_HACKS_FILE ),
				[],
				\JOOST_COMMENT_HACKS_VERSION
			);

			\wp_enqueue_script(
				'yoast-comment-hacks-admin-js',
				\plugins_url( 'admin/assets/js/yoast-comment-hacks' . $min . '.js', \JOOST_COMMENT_HACKS_FILE ),
				[],
				\JOOST_COMMENT_HACKS_VERSION,
				true
			);
		}
	}

	/**
	 * Saves the comment email recipients post meta.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_reroute_comment_emails( $post_id ): void {

		if ( ! isset( $_POST['comment_notification_recipient'] ) || ! \wp_verify_nonce( \filter_input( \INPUT_POST, 'comment_notification_recipient_nonce' ), 'comment_notification_recipient_nonce' ) ) {
			return;
		}

		$recipient_id = (int) \sanitize_key( \wp_unslash( $_POST['comment_notification_recipient'] ) );
		if ( $recipient_id > 0 ) {
			\update_post_meta( $post_id, self::NOTIFICATION_RECIPIENT_KEY, $recipient_id );
		}
	}

	/**
	 * Validate the input, make sure comment length is an integer and above the minimum value.
	 *
	 * @since 1.0
	 *
	 * @param array $input Input with unvalidated options.
	 */
	public function options_validate( array $input ): array {
		$defaults = Hacks::get_defaults();

		foreach ( $input as $key => $value ) {
			switch ( $key ) {
				case 'mincomlength':
				case 'maxcomlength':
				case 'redirect_page':
				case 'comment_policy_page':
					$input[ $key ] = (int) $value;
					break;
				case 'version':
					$input[ $key ] = JOOST_COMMENT_HACKS_VERSION;
					break;
				case 'comment_policy':
				case 'clean_emails':
				case 'disable_email_all_commenters':
					$input[ $key ] = $this->sanitize_bool( $value );
					break;
				case 'email_subject':
				case 'email_body':
				case 'mass_email_body':
				case 'forward_name':
				case 'forward_subject':
					$input[ $key ] = $this->sanitize_string( $value, $defaults[ $key ] );
					break;
				case 'forward_email':
				case 'forward_from_email':
					$input[ $key ] = \sanitize_email( $value );
			}
		}

		if ( ( $this->absolute_min + 1 ) > $input['mincomlength'] || empty( $input['mincomlength'] ) ) {
			/* translators: %d is replaced with the minimum number of characters */
			\add_settings_error( $this->hook, 'min_length_invalid', \sprintf( \__( 'The minimum length you entered is invalid, please enter a minimum length above %d.', 'yoast-comment-hacks' ), $this->absolute_min ) );
			$input['mincomlength'] = 15;
		}

		return $input;
	}

	/**
	 * Turns checkbox values into booleans.
	 *
	 * @param mixed $value The input value to cast to boolean.
	 */
	private function sanitize_bool( $value ): bool {
		if ( $value ) {
			$value = true;
		}
		if ( empty( $value ) ) {
			$value = false;
		}

		return $value;
	}

	/**
	 * Turns empty string into defaults.
	 *
	 * @param mixed  $value   The input value.
	 * @param string $default The default value of the string.
	 *
	 * @return array $input The array with sanitized input values.
	 */
	private function sanitize_string( $value, $default ) {
		if ( $value === '' ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Register the config page for all users that have the manage_options capability.
	 */
	public function add_config_page() {
		\add_options_page(
			\__( 'Comment Hacks', 'yoast-comment-hacks' ),
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
	 */
	public function filter_plugin_actions( $links, $file ): array {
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
	 */
	public function config_page(): void {
		require_once \JOOST_COMMENT_HACKS_PATH . 'admin/views/config-page.php';

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
