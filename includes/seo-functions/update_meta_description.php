<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function update_meta_description() {
    return [
        'title' => 'Automatically update the Rank Math meta description of posts',
        'description' => 'Automatically update the Rank Math meta description of posts, if no meta description is set. (Required: Rank Math SEO Plugin)',
        'inputs' => [
            [
                'type' => 'checkbox',
                'label' => 'Update meta description',
                'name' => 'enabled',
                'default' => 1  // Check the box by default (optional, omit if you want it unchecked by default)
            ],
        ],
        'category' => 'SEO',
        'priority' => 10, // lower priority means earlier execution
    ];
}

// Applied function (apply + file name)
function apply_update_meta_description() {
    new RankMathUpdateController();
}

/**
 * Function to automatically update the Rank Math's meta description of posts, if no meta description is set
 */

class RankMathUpdateController {
    public function __construct() {
        add_action('save_post', [$this, 'update_rank_math_meta'], 10, 2);
    }

    function update_rank_math_meta($post_id, $post) {
        // If this is an autosave, skip the update
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check if the post is a revision
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Update the Rank Math meta description
        $this->add_to_rank_math_seo($post_id);
    }

    function add_to_rank_math_seo($post_id) {
        // Check if the meta description already exists
        $existing_meta_desc = get_post_meta($post_id, 'rank_math_description', true);

        // If the meta description is not set, update it
        if (empty($existing_meta_desc)) {
            // Get the post content
            $post_content = get_post_field('post_content', $post_id);

            // Generate a meta description from the post content
            $metadesc = wp_trim_words($post_content, 25, '...');

            // Update the Rank Math SEO meta description
            $updated_desc = update_post_meta($post_id, 'rank_math_description', $metadesc);

            return $updated_desc;
        }
    }
}

