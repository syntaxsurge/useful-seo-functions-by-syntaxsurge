<?php

// Handles admin settings page rendering and saving settings

class SEO_Settings {
    private $options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
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
        add_settings_section( 'setting_section_id', 'Settings', null, 'seo-functions' );

        $dir = plugin_dir_path( __FILE__ ) . 'seo-functions/';
        foreach ( scandir( $dir ) as $file ) {
            if ( '.php' === substr( $file, -4 ) ) {
                $func_name = str_replace( '.php', '', $file );
                add_settings_field( $func_name, ucfirst( $func_name ), array( $this, 'field_callback' ), 'seo-functions', 'setting_section_id', $func_name );
            }
        }
    }

    public function sanitize( $input ) {
        $new_input = array();
        foreach ( $input as $key => $value ) {
            $new_input[$key] = ( isset( $input[$key] ) && $input[$key] == 1 ) ? 1 : 0;
        }
        return $new_input;
    }

    public function field_callback( $arg ) {
        $v = isset( $this->options[$arg] ) ? $this->options[$arg] : 0;
        printf( '<input type="checkbox" id="%1$s" name="useful_seo_functions[%1$s]" value="1" %2$s>', $arg, checked( 1, $v, false ) );
    }
}
