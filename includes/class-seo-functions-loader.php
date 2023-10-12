<?php

// Loads and initializes all SEO functions from /seo-functions/

class SEO_Functions_Loader {
    private $options;

    public function __construct() {
        $this->options = get_option( 'useful_seo_functions', array() );
        add_action( 'init', array( $this, 'load_seo_functions' ) );
    }

    public function load_seo_functions() {
        $dir = plugin_dir_path( __FILE__ ) . 'seo-functions/';
        foreach ( scandir( $dir ) as $file ) {
            if ( '.php' === substr( $file, -4 ) ) {
                $func_name = str_replace( '.php', '', $file );
                if ( isset( $this->options[$func_name] ) && $this->options[$func_name] ) {
                    include $dir . $file;
                }
            }
        }
    }
}
