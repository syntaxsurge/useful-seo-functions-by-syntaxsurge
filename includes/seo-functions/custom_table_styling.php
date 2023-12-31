<?php

// Ensure WordPress is running to prevent direct access
defined('ABSPATH') || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function custom_table_styling() {
    return [
        'title' => 'Custom Table Styling',
        'description' => 'Enable custom styling for tables and specify colors for table headers.',
        'inputs' => [
            [
                'type' => 'checkbox',
                'label' => 'Enable custom table styling',
                'name' => 'enabled',
                'default' => 1  // Checked by default
            ],
            [
                'type' => 'text',
                'label' => 'Table Header Background Color (e.g., #007BFF): ',
                'name' => 'header_color',
                'default' => '#007BFF'  // Default background color
            ],
            [
                'type' => 'text',
                'label' => 'Table Header Text Color (e.g., #FFFFFF): ',
                'name' => 'text_color',
                'default' => '#FFFFFF'  // Default text color
            ],
        ],
        'category' => 'Design Settings',
        'priority' => 20,
    ];
}

// Applied function (apply + file name)
function apply_custom_table_styling()
{
    add_action('wp_head', 'inject_custom_table_styles');
}

function inject_custom_table_styles() {
    // Get all SEO function options
    $options = get_option('useful_seo_functions');

    // Check if custom table styling is enabled
    $is_enabled = $options['custom_table_styling']['enabled'] ?? true; // Default to true if not set
    $header_color = $options['custom_table_styling']['header_color'] ?? '#007BFF';
    $text_color = $options['custom_table_styling']['text_color'] ?? '#FFFFFF'; // Retrieve the text color

    if ($is_enabled) {

        // Inject a style tag with the dynamic header and text colors
        echo "<style>
                th {
                    background-color: $header_color !important;
                    color: $text_color !important;
                    border-bottom: 3px solid #0056b3;
                    border-top-left-radius: 8px;
                    border-top-right-radius: 8px;
                }
              </style>";

        // Enqueue your external CSS file
        wp_enqueue_style('custom-table-styles', USEFUL_SEO_FUNCTIONS_PLUGIN_URL . 'public/css/custom-table.css', array(), USF_ASSETS_VERSION);
    }
}
