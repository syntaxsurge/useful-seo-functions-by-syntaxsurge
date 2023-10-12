<?php
/**
 * Plugin Name: Useful SEO Functions by SyntaxSurge
 * Plugin URI: https://syntaxsurge.com/
 * Description: This plugin provides useful SEO functions for your WordPress site.
 * Version: 1.0.0
 * Author: SyntaxSurge
 * Author URI: https://syntaxsurge.com
 * License: GPLv2 or later
 * Text Domain: useful-seo-functions-by-syntaxsurge
 */

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define('USEFUL_SEO_FUNCTIONS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once plugin_dir_path( __FILE__ ) . 'includes/class-seo-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-seo-functions-loader.php';

function useful_seo_functions_activation() {
    add_option( 'useful_seo_functions', array() );
}
register_activation_hook( __FILE__, 'useful_seo_functions_activation' );

function initialize_seo_plugin() {
    if ( is_admin() ) {
        $seo_settings = new SEO_Settings();
    }

    $seo_functions_loader = new SEO_Functions_Loader();
}
add_action('plugins_loaded', 'initialize_seo_plugin');
