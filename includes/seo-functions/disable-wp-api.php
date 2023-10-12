<?php

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
            __( 'Protected by SyntaxSurge.com' ),
            array( 'status' => 401 )
        );
    }

    // Our custom authentication check should have no effect
    // on logged-in requests
    return $result;
});
