<?php

/**
 * Plugin Name: Useful SEO Functions by SyntaxSurge
 * Plugin URI: https://serpcraft.com/
 * Description: This plugin provides useful SEO functions for your WordPress site.
 * Version: 1.0.9
 * Author: SyntaxSurge
 * Author URI: https://syntaxsurge.com
 * License: GPLv2 or later
 * Text Domain: useful-seo-functions-by-syntaxsurge
 */

// Ensure Wordpress is running to prevent direct access
defined('ABSPATH') || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define('USEFUL_SEO_FUNCTIONS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('USF_ASSETS_VERSION', '1.2.0');

require_once plugin_dir_path(__FILE__) . 'includes/class-seo-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-seo-functions-loader.php';

// Enqueue your external global CSS file
wp_enqueue_style('usf-global-styles', USEFUL_SEO_FUNCTIONS_PLUGIN_URL . 'public/css/usf-global.css', array(), USF_ASSETS_VERSION);

// Get all default seo function values
function get_default_seo_settings()
{
    $dir = plugin_dir_path(__FILE__) . 'includes/seo-functions/';
    $default_settings = [];

    foreach (scandir($dir) as $file) {
        if ('.php' === substr($file, -4)) {
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
function activate_useful_seo_functions()
{
    $default_settings = get_default_seo_settings();

    // Check if 'useful_seo_functions' option already exists
    if (get_option('useful_seo_functions') !== false) {
        // Delete the existing 'useful_seo_functions' option
        delete_option('useful_seo_functions');
    }

    // Now set the new default settings
    update_option('useful_seo_functions', $default_settings);
}

// Trigger on plugin activation
register_activation_hook(__FILE__, 'activate_useful_seo_functions');

// Initialize includes
function initialize_seo_plugin()
{
    if (is_admin()) {
        $seo_settings = new SEO_Settings();
    }

    $seo_functions_loader = new SEO_Functions_Loader();
}
add_action('plugins_loaded', 'initialize_seo_plugin');


// Hook into the filter for the plugin.
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'seo_functions_add_settings_link');

function seo_functions_add_settings_link($links)
{
    // Build and escape the URL to your settings page.
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=seo-functions')) . '">' . __('Settings', 'text-domain') . '</a>';

    // Add your settings link to the array of links.
    array_unshift($links, $settings_link);

    return $links;
}


// Function to ensure Authorization header is passed through
function ensure_http_authorization_header()
{
    // Check if the Authorization header is set in the request
    if (isset($_SERVER['HTTP_AUTHORIZATION']) || isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        // Do nothing if it's already set
        return;
    }

    // Check for the Authorization header under different server environments
    if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        // Set it as HTTP_AUTHORIZATION if found under REDIRECT_HTTP_AUTHORIZATION
        $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        // Set it as HTTP_AUTHORIZATION if found directly under HTTP_AUTHORIZATION
        $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['HTTP_AUTHORIZATION'];
    }
}

// Add the function to the 'init' hook
add_action('init', 'ensure_http_authorization_header');


function custom_header_authenticate($user)
{
    // No authentication for non-REST requests
    if (!defined('REST_REQUEST') || !REST_REQUEST) {
        return $user;
    }

    // Check for existence of custom header 'Serp-Craft'
    $custom_header = isset($_SERVER['HTTP_SERP_CRAFT']) ? $_SERVER['HTTP_SERP_CRAFT'] : null;

    if (!$custom_header) {
        return $user;
    }

    // Decode the header value (assuming it's base64-encoded username:application_password)
    list($username, $app_password) = explode(':', base64_decode($custom_header), 2);

    // Authenticate using application password
    $user = wp_authenticate_application_password(null, $username, $app_password);

    if (is_wp_error($user)) {
        return null;
    }

    return $user->ID;
}

add_filter('determine_current_user', 'custom_header_authenticate');


// Hook to register new API routes
add_action('rest_api_init', 'register_serpcraft_api_routes');

function register_serpcraft_api_routes()
{
    // Register '/wp-json/serpcraft/v1/posts' endpoint with support for all request types
    register_rest_route('serpcraft/v1', '/posts', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'handle_wp_v2_posts',
        'permission_callback' => '__return_true', // Add this line to make the route publicly accessible
    )
    );

    // Register '/wp-json/serpcraft/v1/media' endpoint with support for all request types
    register_rest_route('serpcraft/v1', '/media', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'handle_wp_v2_media',
        'permission_callback' => '__return_true', // Add this line to make the route publicly accessible
    )
    );

    // Register '/wp-json/serpcraft/v1/categories' endpoint with support for all request types
    register_rest_route('serpcraft/v1', '/categories', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'handle_wp_v2_categories',
        'args' => array(
            'per_page' => array(
                'default' => 100,
            ),
        ),
        'permission_callback' => '__return_true', // Add this line to make the route publicly accessible
    )
    );
}

// Callback function to handle all types of requests for '/wp-json/serpcraft/v1/posts'
function handle_wp_v2_posts($request)
{
    $posts_controller = new WP_REST_Posts_Controller('post');
    switch ($request->get_method()) {
        case 'GET':
            return $posts_controller->get_items($request);
        case 'POST':
            return $posts_controller->create_item($request);
        // Add more cases for PUT, DELETE, etc. as needed
    }
}
