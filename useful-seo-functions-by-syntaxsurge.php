<?php
/**
 * Plugin Name: Useful SEO Functions by SyntaxSurge
 * Plugin URI: https://syntaxsurge.com/
 * Description: This plugin provides useful SEO functions for your WordPress site.
 * Version: 1.0.0
 * Author: SyntaxSurge
 * Author URI: https://syntaxsurge.com
 * License: GPLv2 or later
 * Text Domain: useful-seo-functions-by-syntaxsurge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Include the main plugin class file
require_once plugin_dir_path( __FILE__ ) . 'includes/class-useful-seo-functions.php';

// Plugin activation hook
function useful_seo_functions_activation() {
	$default_options = array(
		'enable_function_1' => 1,
		'enable_function_2' => 1,
		'enable_function_3' => 1,
	);

	if ( false === get_option( 'useful_seo_functions' ) ) {
		add_option( 'useful_seo_functions', $default_options );
	}
}
register_activation_hook( __FILE__, 'useful_seo_functions_activation' );



function prevent_publish_short_post($post_ID, $post, $update) {
    $content_length = strlen(wp_strip_all_tags($post->post_content));
    if (($content_length < 100) && $post->post_status == 'publish' && $post->post_type == 'post') {
        // Unhook this function to prevent infinite loop
        remove_action('save_post', 'prevent_publish_short_post');

        // Update the post to draft status
        wp_update_post(array('ID' => $post_ID, 'post_status' => 'draft'));

        // Re-hook this function
        add_action('save_post', 'prevent_publish_short_post', 10, 3);
    }
}
add_action('save_post', 'prevent_publish_short_post', 10, 3);




function alink_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'href' => '',
            'text' => '',
        ), 
        $atts, 
        'alink' 
    );

    // Sanitize URL and text
    $href = esc_url(base64_decode($atts['href']));
    $text = sanitize_text_field(base64_decode($atts['text']));

    // Create and return the link
    $link = "<a href='{$href}' target='_blank'>{$text}</a>";
    return $link;
}
add_shortcode('alink', 'alink_shortcode');

function important_message($atts) {
    $atts = shortcode_atts(
        array(
            'message' => parse_url(get_site_url(), PHP_URL_HOST), // Default message
            'color' => 'red', // Default color
        ), 
        $atts,
        'important'
    );

    return '
    <div style="background-color: #fff; padding: 30px; margin-bottom: 70px; margin-top: 70px; text-align: center; font-size: 18px; box-shadow: 3px 3px 10px 2px #ecebeb; border-top: 7px solid ' . sanitize_text_field(base64_decode($atts['color'])) . ';">
        <span><strong>' . sanitize_text_field(base64_decode($atts['message'])) . '</strong></span>
    </div>
    ';
}
add_shortcode( 'important', 'important_message' );
