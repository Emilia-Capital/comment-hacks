<?php

namespace JoostBlog\WP\Comment\Tests;

use Yoast\WPTestUtils\WPIntegration;

// Disable xdebug backtrace.
if ( \function_exists( 'xdebug_disable' ) ) {
	\xdebug_disable();
}

echo 'Welcome to the Comment Hacks Test Suite' . \PHP_EOL;
echo 'Version: 1.0' . \PHP_EOL . \PHP_EOL;

require_once \dirname( __DIR__ ) . '/vendor/yoast/wp-test-utils/src/WPIntegration/bootstrap-functions.php';

// Give access to tests_add_filter() function.
require_once \rtrim( WPIntegration\get_path_to_wp_test_dir(), '/' ) . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function manually_load_plugin() {
	require \dirname( __DIR__ ) . '/comment-hacks.php';
}

/**
 * Filter the plugins URL to pretend the plugin is installed in the test environment.
 *
 * @param string $url    The complete URL to the plugins directory including scheme and path.
 * @param string $path   Path relative to the URL to the plugins directory. Blank string
 *                       if no path is specified.
 * @param string $plugin The plugin file path to be relative to. Blank string if no plugin
 *                       is specified.
 *
 * @return string
 */
function plugins_url( $url, $path, $plugin ) {
	$plugin_dir = \dirname( __DIR__ );
	if ( $plugin === $plugin_dir . '/comment-hacks.php' ) {
		$url = \str_replace( \dirname( $plugin_dir ), '', $url );
	}

	return $url;
}

// Add plugin to active mu-plugins - to make sure it gets loaded.
\tests_add_filter( 'muplugins_loaded', __NAMESPACE__ . '\manually_load_plugin' );

// Overwrite the plugin URL to not include the full path.
\tests_add_filter( 'plugins_url', __NAMESPACE__ . '\plugins_url', 10, 3 );

/*
 * Load WordPress, which will load the Composer autoload file, and load the MockObject autoloader after that.
 */
WPIntegration\bootstrap_it();
