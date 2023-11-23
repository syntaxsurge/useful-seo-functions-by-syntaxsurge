<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function enable_meta_description_api() {
    return [
        'title' => 'Enable the Rank Math Meta Description of Posts via API',
        'description' => 'Enable the Rank Math Meta Description of Posts via API. (Required: Rank Math SEO Plugin)',
        'inputs' => [
            [
                'type' => 'checkbox',
                'label' => 'Enable Description API',
                'name' => 'enabled',
                'default' => 1  // Check the box by default (optional, omit if you want it unchecked by default)
            ],
        ],
        'category' => 'SEO Settings',
        'priority' => 10, // lower priority means earlier execution
    ];
}

// Applied function (apply + file name)
function apply_enable_meta_description_api() {
    add_action( 'rest_insert_post', 'update_rank_math_description_via_api', 10, 3 );
}

/**
Function to set Rank Math meta description for a post via API
Sample usage:
'meta': {'rank_math_description': meta_description}
**/
function update_rank_math_description_via_api( $post, $request, $creating ) {
    $meta_description = isset($request->get_param('meta')['rank_math_description']) 
        ? $request->get_param('meta')['rank_math_description'] 
        : null;

    if ( ! empty( $meta_description ) ) {
        update_post_meta( $post->ID, 'rank_math_description', sanitize_text_field( $meta_description ) );
    }
}
