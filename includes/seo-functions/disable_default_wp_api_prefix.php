<?php
// Ensure WordPress is defined to prevent direct access.
defined('ABSPATH') || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as the file name
function disable_default_wp_api_prefix()
{
    return [
        'title' => 'Disable Default WP API Prefix',
        'description' => 'Disables the default `wp-json` REST API prefix to prevent public access to the WordPress REST API under the default namespace. This ensures that REST API endpoints are only accessible through custom prefixes, enhancing the security of your WordPress site by obscuring the standard REST API entry points. However, do not enable this feature if you are using plugins or software such as Elementor, Site Kit by Google, or any others that rely on the default WordPress API prefix. These tools require the standard `wp-json` prefix to function correctly. If this feature is enabled, you can only use the /serpcraft/wp/v2/ endpoint for the API.',
        'inputs' => [
            [
                'type' => 'checkbox',
                'label' => 'Disable default "wp-json" prefix',
                'name' => 'enabled',
                'default' => 0  // Check the box by default (optional, set 0 if you want it unchecked by default)
            ],
        ],
        'category' => 'Security Settings',
        'priority' => 10, // lower priority means earlier execution
    ];
}

// Applied function (apply + file name)
function apply_disable_default_wp_api_prefix()
{
    // Hook into 'rest_pre_serve_request' to handle the request before sending it.
    add_filter('rest_pre_serve_request', function ($served, $result, $request, $server) {
        // Get the full request URI
        $request_uri = $_SERVER['REQUEST_URI'];

        // Define the application passwords REST API route.
        // Use regex to match the full endpoint pattern for user application passwords, where (\d+) is the user ID placeholder.
        // Adjust the regex pattern if your user IDs have different requirements or if the structure of the URL is different.
        $app_passwords_route_regex = '/wp/v2/users/\d+/application-passwords';

        // Check if the request is for application passwords.
        // Use preg_match() to test if the request URI matches our defined route regex.
        if (preg_match('#' . $app_passwords_route_regex . '#', $request_uri)) {
            // If it's a request for application passwords, allow it by returning false.
            return false;
        }

        // Check if the request starts with the '/wp-json' prefix.
        if (stripos($request_uri, '/wp-json') !== false) {
            // Issue a 403 Forbidden response because the 'wp-json' prefix should not be accessible except for application passwords.
            header('HTTP/1.1 403 Forbidden');
            // Send a message and terminate the script.
            die('Access to the default REST API prefix has been disabled except for Application Passwords. Protected by Syntaxsurge.com and Serpcraft.com');
        }

        // If none of the above checks match, pass through the result for WordPress to serve normally.
        return $served;
    }, 10, 4);
}
