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

 if ( ! defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path( __FILE__ ) . 'includes/class-seo-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-seo-functions-loader.php';

function useful_seo_functions_activation() {
    add_option( 'useful_seo_functions', array() );
}
register_activation_hook( __FILE__, 'useful_seo_functions_activation' );

if ( is_admin() ) {
    $seo_settings = new SEO_Settings();
}

$seo_functions_loader = new SEO_Functions_Loader();
