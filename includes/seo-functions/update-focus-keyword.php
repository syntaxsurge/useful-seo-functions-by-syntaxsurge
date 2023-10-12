<?php

/**
 * Function to automatically update the focus keyword with the post slug, if no focus keyword is set
 */
function update_focus_keywords($post_id) {
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

add_action('save_post', 'update_focus_keywords');
