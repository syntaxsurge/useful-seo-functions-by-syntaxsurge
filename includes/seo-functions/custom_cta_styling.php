<?php

// Ensure WordPress is running to prevent direct access
defined('ABSPATH') || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function custom_cta_styling() {
    return [
        'title' => 'Custom CTA Button Styling',
        'description' => 'Customize the appearance of CTA buttons.',
        'inputs' => [
            [
                'type' => 'text',
                'label' => 'CTA Button Background Color (e.g., #b11717): ',
                'name' => 'cta_bg_color',
                'default' => '#b11717'  // Default background color for CTA button
            ],
            [
                'type' => 'text',
                'label' => 'CTA Button Text Color (e.g., #FFFFFF): ',
                'name' => 'cta_text_color',
                'default' => '#FFFFFF'  // Default text color for CTA button
            ],
            // Add more customization fields as needed
        ],
        'category' => 'Design Settings',
        'priority' => 20,
    ];
}

// Applied function (apply + file name)
function apply_custom_cta_styling()
{
    add_action('wp_head', 'inject_custom_cta_styles');
}

function inject_custom_cta_styles() {
    // Get all SEO function options
    $options = get_option('useful_seo_functions');

    $cta_bg_color = $options['custom_cta_styling']['cta_bg_color'] ?? '#b11717';
    $cta_text_color = $options['custom_cta_styling']['cta_text_color'] ?? '#FFFFFF';

    // Inject a style tag with the dynamic CTA button styles
    echo "<style>
            .cta-button {
                background-color: $cta_bg_color !important;
                color: $cta_text_color !important;
                padding: 12px 24px;
                border: none;
                cursor: pointer;
                font-size: 18px;
                font-weight: bold;
                border-radius: 5px;
                text-decoration: none;
                display: inline-block;
                transition: background-color 0.3s, transform 0.3s;
            }
            .cta-button:hover, .cta-button:focus {
                transform: scale(1.05);
                outline: none;
            }
          </style>";
}
