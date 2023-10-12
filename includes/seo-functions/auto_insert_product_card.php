<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function auto_insert_product_card() {
    return [
        'title' => 'Auto insert product card shortcode below the header',
        'description' => 'Auto insert custom product card below the header and above the article tag via javascript.',
        'inputs' => [
            [
                'type' => 'checkbox',
                'label' => 'Auto insert product card',
                'name' => 'auto_insert_product_card',
                'default' => 1  // Check the box by default (optional, omit if you want it unchecked by default)
            ],
        ],
        'category' => 'Affiliate Settings',
        'priority' => 10, // lower priority means earlier execution
    ];
}

// Applied function (apply + file name)
function apply_auto_insert_product_card() {
    add_filter('the_content', 'cpc_add_shortcode_to_content');
}

// Auto add Amazon Product search link based on post's slug
function cpc_add_shortcode_to_content($content) {
    // Ensure we're on a single post and the setting is enabled.
    if (is_single()) {
        $slug = basename(get_permalink());
        $name = str_replace('-', ' ', $slug);
                
        // URL encode the name for use in a query parameter
        $encoded_name = urlencode(ucwords($name));
        
        $shortcode = sprintf('[product_card img1="https://www.amazon.com/favicon.ico" link="https://www.amazon.com/s?k=%s%s" name="%s" show_tag="true" tag_text="Search Related Amazon Product"]',
            $encoded_name, $affiliate_parameter, ucwords($name));

        // Wrap the shortcode in a div with an ID
        $new_content = '<div id="cpc-injected-content">' . do_shortcode($shortcode) . '</div>' . $content;

        // Add JS to move the shortcode output around
        $new_content .= '
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {
                    // Find the injected content and the article element
                    var injectedContent = document.getElementById("cpc-injected-content");
                    var articleElement = document.querySelector("article");  // General assumption
            
                    // Ensure elements are found
                    if (injectedContent && articleElement) {
                        // Insert the injected content before the article
                        articleElement.parentNode.insertBefore(injectedContent, articleElement);
                    }
                });
            </script>
        ';

        return $new_content;
    }
    return $content;
}
