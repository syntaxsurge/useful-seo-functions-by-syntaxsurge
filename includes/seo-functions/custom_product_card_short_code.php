<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function custom_product_card_short_code() {
    return [
        'title' => 'Add a product card via a shortcode',
        'description' => "If there is already an affiliate tag, it will be replaced if it is not the desired one. The relationship between the current page and the linked Amazon affiliate page is automatically set to 'sponsored' and 'no follow' (rel='nofollow sponsored').",
        'inputs' => [
            [
                'type' => 'checkbox',
                'label' => 'Add product card',
                'name' => 'enabled',
                'default' => 1  // Check the box by default (optional, omit if you want it unchecked by default)
            ],
        ],
        'category' => 'Affiliate Settings',
        'priority' => 10, // lower priority means earlier execution
    ];
}

// Applied function (apply + file name)
function apply_custom_product_card_short_code() {
    // Load CSS styles for product cards
    if ( ! function_exists('cpc_enqueue_styles') ) {
        function cpc_enqueue_styles() {
            wp_enqueue_style('cpc_styles', USEFUL_SEO_FUNCTIONS_PLUGIN_URL . 'assets/css/product-cards.css', array(), USF_ASSETS_VERSION);
        }
        add_action('wp_enqueue_scripts', 'cpc_enqueue_styles');
    }
    
    // Load the main shortcode logic
    add_shortcode('product_card', 'cpc_shortcode');
}


// Short code Logic
function cpc_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'link' => 'https://syntaxsurge.com',
            'img1' => '',
            'img2' => '',
            'name' => '',
            'show_tag' => 'true',
            'tag_text' => 'RECOMMENDED PRODUCT' // Added the 'tag_text' attribute
        ),
        $atts,
        'product_card'
    );

    $link = esc_url($atts['link']);
    $img1 = esc_url($atts['img1']);
    $img2 = esc_url($atts['img2']);
    $name = sanitize_text_field($atts['name']);
    $show_tag = filter_var($atts['show_tag'], FILTER_VALIDATE_BOOLEAN);
    $tag_text = sanitize_text_field($atts['tag_text']); // Sanitizing the 'tag_text'

    // Check if name is empty and link contains amazon.com/s
    if(empty($name) && strpos($link, 'amazon.com/s') !== false) {
        // Parse the URL and query string
        $parsed_url = parse_url($link);
        parse_str($parsed_url['query'], $query_params);

        // Check if the 'k' query parameter exists and is not empty
        if(!empty($query_params['k'])) {
            // Decode the URL parameter, convert to title case, and sanitize for use
            $name = sanitize_text_field(ucwords(urldecode($query_params['k'])));
        }
    }
    
    // If name is still empty, then make the URL the Name
    if(empty($name) && !empty($link)) {
        $name = $link;
    }

    ob_start();
    ?>
    <a href="<?php echo $link; ?>" rel="nofollow sponsored" class="product-card-link" target="_blank">
        <div class="product-card">
            <?php 
            // Check if tag should be displayed
            if($show_tag) : 
            ?>
                <div class="recommendation-tag"><?php echo esc_html($tag_text); ?></div>
            <?php 
            endif; 
            ?>
            <div class="product-content">
                <div class="product-images">
                    <?php 
                    // Check if img1 is provided and render image
                    if (!empty($img1)) : 
                    ?>
                        <img src="<?php echo $img1; ?>" alt="Product 1">
                    <?php 
                    endif; 

                    // Check if img2 is provided and render image
                    if (!empty($img2)) : 
                    ?>
                        <img src="<?php echo $img2; ?>" alt="Product 2">
                    <?php 
                    endif; 
                    ?>
                </div>
                <div class="product-name"><?php echo esc_html($name); ?></div>
            </div>
        </div>
    </a>
    <?php
    return ob_get_clean();
}
