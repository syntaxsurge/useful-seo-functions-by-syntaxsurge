<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function auto_amazon_affiliate_id() {
    return [
        'title' => 'Adds your Amazon affiliate ID to all Amazon links in your content',
        'description' => 'Replaces existing affiliate tags if they are not the desired one.',
        'inputs' => [
            [
                'type' => 'text',
                'label' => 'Amazon Affiliate ID: ',
                'name' => 'auto_amazon_affiliate_id',
                'default' => 'jadelempleo-20'
            ],
        ],
        'category' => 'Affiliate Settings',
        'priority' => 999, // lower priority means earlier execution
    ];
}

// Applied function (apply + file name)
function apply_auto_amazon_affiliate_id() {
    // Hook the function into 'the_content' filter
    add_filter('the_content', 'amazon_affiliate_id_injector', 999);
}

function amazon_affiliate_id_injector($content) {
    // Get all SEO function options
    $options = get_option('useful_seo_functions');
    
    // Get our affiliate ID
    $affiliate_id = $options['auto_amazon_affiliate_id']['auto_amazon_affiliate_id'] ?? '';
    
    // Ensure we have an affiliate ID to use
    if (empty($affiliate_id)) {
        return $content;
    }
    
    // Matching Amazon links
    $pattern = '/<a(.*?)href=["\'](https:\/\/www\.amazon\.com(\/[A-Za-z0-9]+)*\/?[?&]?)([^"\']*?)["\'](.*?)>/i';
    
    // Replace callback
    return preg_replace_callback($pattern, function($matches) use ($affiliate_id) {
        $url = $matches[2] . $matches[4];
        
        // If there's already an affiliate tag
        if (strpos($url, 'tag=') !== false) {
            // Replace it if it's not the desired one
            return preg_replace('/tag=[A-Za-z0-9\-_]+/', 'tag=' . $affiliate_id, $matches[0]);
        } else {
            // Append the affiliate tag
            $connector = (strpos($url, '?') !== false) ? '&' : '?';
            return '<a' . $matches[1] . 'href="' . $url . $connector . 'tag=' . $affiliate_id . '"' . $matches[5] . '>';
        }
    }, $content);
}
