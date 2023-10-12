<?php

/**
 * Function to automatically update the meta description of posts, if no meta description is set
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

new RankMathUpdateController();
