<?php
/**
 * Plugin Name:       JS Categories List Block
 * Plugin URI:        https://skatox.com/blog/jquery-categories-list-widget/
 * Description:       A widget for displaying a category list with some effects.
 * Version:           4.1.0
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Author:            Miguel Angel Useche Castro
 * Author URI:        https://migueluseche.com/
 * Text Domain:       jquery-categories-list
 * Domain Path:       /languages
 * License: GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Copyleft 2009-2026  Miguel Angel Useche Castro (email : migueluseche@skatox.com)
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

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'JCL_ROOT_PATH' ) ) {
	define( 'JCL_ROOT_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'JCL_BASE_URL' ) ) {
	define( 'JCL_BASE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'JCL_VERSION' ) ) {
	define( 'JCL_VERSION', '4.1.0' );
}

if ( ! defined( 'JCL_TEXT_DOMAIN' ) ) {
	define( 'JCL_TEXT_DOMAIN', 'jquery-categories-list' );
}

require_once JCL_ROOT_PATH . 'classes/legacy/class-jcl-legacy-html-builder.php';
require_once JCL_ROOT_PATH . 'classes/legacy/class-jcl-legacy-widget.php';
require_once JCL_ROOT_PATH . 'classes/class-js-categories-list-rest-endpoints.php';
require_once JCL_ROOT_PATH . 'classes/class-js-categories-list-block.php';

/**
 * Loads the plugin translations.
 *
 * @return void
 */
function jcl_load_textdomain() {
	load_plugin_textdomain( JCL_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Registers the block and its translated script strings.
 *
 * @return void
 */
function jcl_create_widget_block() {
	register_block_type(
		__DIR__ . '/build',
		[
			'render_callback' => [ JS_Categories_List_Block::instance(), 'build_html' ],
		]
	);

	if ( function_exists( 'generate_block_asset_handle' ) ) {
		wp_set_script_translations(
			generate_block_asset_handle( 'jquery-categories-list/categories-block', 'editorScript' ),
			JCL_TEXT_DOMAIN,
			JCL_ROOT_PATH . 'languages'
		);
	}
}

add_action( 'init', 'jcl_load_textdomain' );
add_action( 'init', 'jcl_create_widget_block' );
