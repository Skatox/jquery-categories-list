<?php
/**
 * Plugin Name:       JS Categories List Block
 * Plugin URI:        https://skatox.com/blog/jquery-categories-list-widget/
 * Description:       A widget for displaying a category list with some effects.
 * Version:           4.0.0
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Author:            Miguel Angel Useche Castro
 * Author URI:        https://migueluseche.com/
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jcl_i18n
 * Domain Path:       /languages
 *
 * @package          jquery-categories-list
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! defined( 'JCL_ROOT_PATH' ) ) {
	define( 'JCL_ROOT_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'JCL_BASE_URL' ) ) {
	define( 'JCL_BASE_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'JCL_VERSION' ) ) {
	define( 'JCL_VERSION', '4.0.0' );
}

require_once( 'classes/class-js-categories-list-options.php' );
require_once( 'classes/class-js-categories-list-rest-endpoints.php' );
require_once( 'classes/class-js-categories-list-block.php' );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function jcl_create_widget_block() {
	register_block_type( __DIR__ . '/build' );
}

add_action( 'init', 'jcl_create_widget_block' );
