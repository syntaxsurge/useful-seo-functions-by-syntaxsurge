<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Handles admin settings page rendering and saving settings

class SEO_Settings {
    private $options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) ); // Enqueue styles
    }
    
    public function enqueue_admin_styles() {
        // Only load styles on your plugin's settings page
        $screen = get_current_screen();
        if ( strpos( $screen->base, 'seo-functions' ) !== false ) {
            wp_enqueue_style('seo_functions_admin_style', USEFUL_SEO_FUNCTIONS_PLUGIN_URL . 'public/css/seo-admin.css', array(), '1.0.2'); // Versioning
        }
    }

    public function add_plugin_page() {
        add_options_page( 'SEO Functions', 'SEO Functions', 'manage_options', 'seo-functions', array( $this, 'create_admin_page' ) );
    }

    public function create_admin_page() {
        $this->options = get_option( 'useful_seo_functions', array() );
        ?>
        <div class="wrap">
            <h1>Useful SEO Functions by SyntaxSurge</h1>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'seo_functions_group' );
                do_settings_sections( 'seo-functions' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting( 'seo_functions_group', 'useful_seo_functions', array( $this, 'sanitize' ) );
    
        $dir = plugin_dir_path( __FILE__ ) . 'seo-functions/';
        $categories = [];
    
        // Iterate through function files and collect categories
        foreach ( scandir( $dir ) as $file ) {
            if ( '.php' === substr( $file, -4 ) ) {
                $func_name = str_replace('.php', '', $file);
                include_once $dir . $file;
                
                if (function_exists($func_name)) {
                    $function_info = call_user_func($func_name);
                    $category = $function_info['category'] ?? 'General';
                    $categories[$category][$func_name] = $function_info;
                }
            }
        }
    
        // Create a section for each category and add fields
        foreach ($categories as $category_name => $functions) {
            add_settings_section( $category_name, $category_name, null, 'seo-functions' );
    
            foreach ($functions as $func_name => $function_info) {
                add_settings_field(
                    $func_name, 
                    $function_info['title'],
                    array($this, 'field_callback'), 
                    'seo-functions', 
                    $category_name, 
                    [
                        'func_name' => $func_name,
                        'description' => $function_info['description'],
                        'inputs' => $function_info['inputs'] ?? []
                    ]
                );
            }
        }
    }

    public function sanitize($input) {
        $new_input = [];
        foreach ($input as $func_name => $settings) {
            foreach ($settings as $key => $value) {
                // You may switch on $key or define type in $function_info and switch on it
                $new_input[$func_name][$key] = sanitize_text_field($value);
            }
        }
        return $new_input;
    }

    public function field_callback($args) {
        $func_name = $args['func_name'];
        $description = $args['description'];
        $inputs = $args['inputs'];
        $options = $this->options[$func_name] ?? [];
        
        // Dynamically generate input fields based on $function_info configuration
        foreach ($inputs as $input) {
            $value = $options[$input['name']] ?? $input['default'];
            
            // Handle different types of input fields
            switch ($input['type']) {
                case 'text':
                    printf(
                        '<label for="%1$s[%2$s]">%3$s</label><input type="text" id="%1$s[%2$s]" name="useful_seo_functions[%1$s][%2$s]" value="%4$s">',
                        $func_name,
                        $input['name'],
                        esc_html($input['label']),
                        esc_attr($value)
                    );
                    break;
                case 'checkbox':
                    printf(
                        '<input type="hidden" name="useful_seo_functions[%1$s][%2$s]" value="0">
                        <input type="checkbox" id="%1$s[%2$s]" name="useful_seo_functions[%1$s][%2$s]" value="1" %3$s>',
                        $func_name,
                        $input['name'],
                        checked(1, $value, false)
                    );
                    break;
                // Add more case blocks for additional input types as needed
            }
            echo '<br>';
        }
        
        // Output description
        printf('<p class="description">%s</p>', esc_html($description));
    }
}
