<?php

// Ensure Wordpress is running to prevent direct access
defined('ABSPATH') || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function change_amazon_domains() {
    return [
        'title' => 'Change Amazon Domains in Content',
        'description' => 'Changes Amazon.com links to a selected Amazon international domain.',
        'inputs' => [
            [
                'type' => 'select',
                'label' => 'Select Amazon Domain: ',
                'name' => 'selected_domain',
                'options' => [
                    'amazon.com', 'amazon.co.uk', 'amazon.ae', 'amazon.com.tr', 'amazon.se', 
                    'amazon.es', 'amazon.sg', 'amazon.sa', 'amazon.pl', 'amazon.nl', 
                    'amazon.com.mx', 'amazon.co.jp', 'amazon.it', 'amazon.in', 'amazon.de', 
                    'amazon.fr', 'amazon.eg', 'amazon.cn', 'amazon.ca', 'amazon.com.br', 
                    'amazon.com.be', 'amazon.com.au'
                ],
                'default' => 'amazon.com'
            ],
        ],
        'category' => 'Affiliate Settings',
        'priority' => 999, // lower priority means earlier execution
    ];
}

// Applied function (apply + file name)
function apply_change_amazon_domains() {
    // Use a lower priority to ensure it runs after shortcodes are processed
    add_filter('the_content', 'amazon_domain_changer', 999);
}

function amazon_domain_changer($content) {
    $options = get_option('useful_seo_functions');
    $selected_domain = $options['change_amazon_domains']['selected_domain'] ?? 'amazon.com';

    $pattern = '/<a\s+[^>]*href=["\'](?:https?:\/\/)?(?:www\.)?amazon\.com(\/[\w\/]*)(?:[?][^"\']*)?["\'][^>]*>/i';

    return preg_replace_callback($pattern, function($matches) use ($selected_domain) {
        return str_replace('amazon.com', $selected_domain, $matches[0]);
    }, $content);
}
