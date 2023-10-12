<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function update_focus_keyword() {
    return [
        'title' => 'Automatically update the Rank Math focus keyword with the post slug',
        'description' => 'Automatically update the Rank Math focus keyword with the post slug (Required: Rank Math SEO Plugin)',
        'inputs' => [
            [
                'type' => 'checkbox',
                'label' => 'Update focus keyword',
                'name' => 'enabled',
                'default' => 1  // Check the box by default (optional, omit if you want it unchecked by default)
            ],
        ],
        'category' => 'SEO',
        'priority' => 10, // lower priority means earlier execution
    ];
}


// Applied function (apply + file name)
function apply_update_focus_keyword() {
    add_action('save_post', 'main_update_focus_keywords');
}


/**
 * Function to automatically update the focus keyword with the post slug, if no focus keyword is set
 */
function main_update_focus_keywords($post_id) {
    // Check if the post is a revision
    if (wp_is_post_revision($post_id)) {
        return;
    }

    $post = get_post($post_id);
    $slug = $post->post_name;
    $slug = str_replace('-', ' ', $slug);

    // Check if Rank Math keyword already exists and only update if it doesn't have it
    $rank_math_keyword = get_post_meta($post_id, 'rank_math_focus_keyword', true);
    if (!$rank_math_keyword) {
        update_post_meta($post_id, 'rank_math_focus_keyword', strtolower($slug));
    }	
}
