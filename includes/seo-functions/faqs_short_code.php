<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Function name should be the same as file name
function faqs_short_code() {
    return [
        'title' => 'FAQs Schortcode',
        'description' => 'Enables shortcodes to structure FAQs to schema for rich results on Google.',
        'inputs' => [
            [
                'type' => 'checkbox',
                'label' => 'FAQs Schortcode',
                'name' => 'enabled',
                'default' => 1  // Check the box by default (optional, omit if you want it unchecked by default)
            ],
        ],
        'category' => 'SEO Settings',
        'priority' => 10, // lower priority means earlier execution
    ];
}

// Applied function (apply + file name)
function apply_faqs_short_code() {
    // Load CSS styles for FAQs
    if ( ! function_exists('faqs_enqueue_styles') ) {
        function faqs_enqueue_styles() {
            wp_enqueue_style('faqs_styles', USEFUL_SEO_FUNCTIONS_PLUGIN_URL . 'public/css/faqs.css', array(), USF_ASSETS_VERSION);
        }
        add_action('wp_enqueue_scripts', 'faqs_enqueue_styles');
    }
    
    add_shortcode('faq', 'faq_shortcode');
    add_shortcode('faq_section', 'faq_section_shortcode');
}


function is_base64($s)
{
    return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s);
}

function faq_shortcode($atts, $content = null)
{
    $defaults = array(
        'question' => '',
        'answer' => '',
    );
    $atts = shortcode_atts($defaults, $atts, 'faq');

    if (is_base64($atts['question'])) {
        $atts['question'] = base64_decode($atts['question']);
    }

    if (is_base64($atts['answer'])) {
        $atts['answer'] = base64_decode($atts['answer']);
    }

    $output = '<div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
        <h3 itemprop="name">' . esc_html($atts['question']) . '</h3>
        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">' . wp_kses_post($atts['answer']) . '</div>
        </div>
    </div>';

    return $output;
}

function faq_section_shortcode($atts, $content = null)
{
    $defaults = array(
        'title' => '',
    );
    $atts = shortcode_atts($defaults, $atts, 'faq_section');

    if (is_base64($atts['title'])) {
        $atts['title'] = base64_decode($atts['title']);
    }

    $output = '<section class="faq-section" itemscope itemtype="https://schema.org/FAQPage">';
    if (!empty($atts['title'])) {
        $output .= '<h2>' . esc_html($atts['title']) . '</h2>';
    }
    $output .= do_shortcode($content);
    $output .= '</section>';

    return $output;
}
