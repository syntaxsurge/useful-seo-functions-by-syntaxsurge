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

// Get all default seo function values
function get_default_seo_settings() {
    $dir = plugin_dir_path( __FILE__ ) . 'includes/seo-functions/';
    $default_settings = [];
    
    foreach ( scandir( $dir ) as $file ) {
        if ( '.php' === substr( $file, -4 ) ) {
            $func_name = str_replace('.php', '', $file);
            require_once $dir . $file;
            
            if (function_exists($func_name)) {
                $function_info = call_user_func($func_name);
                $default_settings[$func_name] = [
                    'enabled' => 1 // Enable all by default
                ];

                // Handle default input values
                if (isset($function_info['inputs']) && is_array($function_info['inputs'])) {
                    foreach ($function_info['inputs'] as $input) {
                        if (isset($input['name'], $input['default'])) {
                            $default_settings[$func_name][$input['name']] = $input['default'];
                        }
                    }
                }
            }
        }
    }
    
    return $default_settings;
}

// Enable All seo functions by default
function activate_useful_seo_functions() {
    $default_settings = get_default_seo_settings();
    
    if (false === get_option('useful_seo_functions')) {
        update_option('useful_seo_functions', $default_settings);
    }
}

// Trigger on plugin activation
register_activation_hook(__FILE__, 'activate_useful_seo_functions');

// Initialize includes
function initialize_seo_plugin() {
    if ( is_admin() ) {
        $seo_settings = new SEO_Settings();
    }

    $seo_functions_loader = new SEO_Functions_Loader();
}
add_action('plugins_loaded', 'initialize_seo_plugin');
