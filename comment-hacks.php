<?php
/**
 * Comment Hacks plugin.
 *
 * @wordpress-plugin
 * Plugin Name:  Comment Hacks
 * Version:      2.1.1
 * Plugin URI:   https://joost.blog/plugins/comment-hacks/
 * Description:  Make comments management easier by applying the simple hacks Joost has gathered over the years.
 * Requires PHP: 7.4
 * Author:       Joost de Valk
 * Author URI:   https://joost.blog/
 * Text Domain:  comment-hacks
 *
 * Copyright 2009-2024 Joost de Valk (email: joost@joost.blog)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

use EmiliaProjects\WP\Comment\Inc\Autoload;
use EmiliaProjects\WP\Comment\Inc\Hacks;

/**
 * Used for version checks.
 */
define( 'EMILIA_COMMENT_HACKS_VERSION', '2.1.1' );

/**
 * Used for asset embedding.
 */
define( 'EMILIA_COMMENT_HACKS_FILE', __FILE__ );

if ( ! defined( 'EMILIA_COMMENT_HACKS_PATH' ) ) {
	define( 'EMILIA_COMMENT_HACKS_PATH', plugin_dir_path( __FILE__ ) );
}

require_once EMILIA_COMMENT_HACKS_PATH . 'inc/autoload.php';
new Autoload();

new Hacks();
