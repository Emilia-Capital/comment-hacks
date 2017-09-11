<?php
/**
 * @package YoastCommentHacks

Plugin Name: Yoast Comment Hacks
Version: 1.4
Plugin URI: https://yoast.com/wordpress/plugins/comment-hacks/
Description: Make comments management easier by applying some of the simple hacks the Yoast team uses.
Author: Team Yoast
Author URI: https://yoast.com/
Text Domain: yoast-comment-hacks

Copyright 2009-2017 Team Yoast (email: support@yoast.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Used for version checks
 */
define( 'YOAST_COMMENT_HACKS_VERSION', '1.4' );

/**
 * Used for asset embedding
 */
define( 'YOAST_COMMENT_HACKS_FILE', __FILE__ );

if ( ! defined( 'YST_COMMENT_HACKS_PATH' ) ) {
	define( 'YST_COMMENT_HACKS_PATH', plugin_dir_path( __FILE__ ) );
}

/* ***************************** CLASS AUTOLOADING *************************** */
if ( file_exists( YST_COMMENT_HACKS_PATH . '/vendor/autoload_52.php' ) ) {
	require YST_COMMENT_HACKS_PATH . '/vendor/autoload_52.php';
}

new YoastCommentHacks();

