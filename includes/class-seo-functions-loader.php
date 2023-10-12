<?php

// Ensure Wordpress is running to prevent direct access
defined( 'ABSPATH' ) || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Loads and initializes all SEO functions from /seo-functions/

class SEO_Functions_Loader {
    private $options;

    public function __construct() {
        $this->options = get_option( 'useful_seo_functions', array() );
        add_action( 'init', array( $this, 'load_seo_functions' ) );
    }

    public function load_seo_functions() {
        $dir = plugin_dir_path( __FILE__ ) . 'seo-functions/';
        $functions = [];

        // Load all function files and retrieve priorities.
        foreach ( scandir( $dir ) as $file ) {
            if ( '.php' === substr( $file, -4 ) ) {
                $func_name = str_replace('.php', '', $file);
                require_once $dir . $file;
                
                // Ensure the function exists in included file
                if (function_exists($func_name)) {
                    $function_info = call_user_func($func_name);
                    $priority = $function_info['priority'] ?? 10; // Default priority is 10
                    $functions[$func_name] = $priority;
                }
            }
        }

        // Sort functions by priority.
        asort($functions);

        // Apply the functions in order of priority.
        foreach ($functions as $func_name => $priority) {
            // Apply the function if the related option is enabled
            if (isset($this->options[$func_name]) && $this->options[$func_name]) 
            {
                // Call main apply function of each php files in seo-functions folder
                call_user_func("apply_$func_name");
            }
        }

    }
}
