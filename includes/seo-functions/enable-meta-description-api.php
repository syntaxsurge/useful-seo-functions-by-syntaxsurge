<?php

/**
Function to set Rank Math meta description for a post via API
Sample usage:
'meta': {'rank_math_description': meta_description}
**/
add_action( 'rest_insert_post', 'update_rank_math_description_via_api', 10, 3 );
function update_rank_math_description_via_api( $post, $request, $creating ) {
    $meta_description = isset($request->get_param('meta')['rank_math_description']) 
        ? $request->get_param('meta')['rank_math_description'] 
        : null;

    if ( ! empty( $meta_description ) ) {
        update_post_meta( $post->ID, 'rank_math_description', sanitize_text_field( $meta_description ) );
    }
}
