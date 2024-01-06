<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function disable_wp_api() {
    return [
        'title' => 'Disable Public View of Wordpress API',
        'description' => 'Disable Wordpress API for unauthorized users or public view. Only logged in users can access the API.',
        'inputs' => [
            [
                'type' => 'checkbox',
                'label' => 'Disable API for public',
                'name' => 'enabled',
                'default' => 1  // Check the box by default (optional, omit if you want it unchecked by default)
            ],
        ],
        'category' => 'Security Settings',
        'priority' => 10, // lower priority means earlier execution
    ];
}

// Applied function (apply + file name)
function apply_disable_wp_api() {
    
    /**
     * Function to disallow Wordpress API on unauthorized users or public view.
     */
    add_filter( 'rest_authentication_errors', function( $result ) {
        // If a previous authentication check was applied,
        // pass that result along without modification.
        if ( true === $result || is_wp_error( $result ) ) {
            return $result;
        }
    
        // No authentication has been performed yet.
        // Return an error if user is not logged in.
        if ( ! is_user_logged_in() ) {
            return new WP_Error(
                'disabled_for_you_by_syntaxsurge',
                __( 'Protected by SyntaxSurge.com and Serpcraft.com' ),
                array( 'status' => 401 )
            );
        }
    
        // Our custom authentication check should have no effect
        // on logged-in requests
        return $result;
    });

}