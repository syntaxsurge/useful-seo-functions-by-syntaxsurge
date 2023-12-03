<?php

// Ensure Wordpress is running to prevent direct access
defined('ABSPATH') || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Loads and initializes all SEO functions from /seo-functions/

class SEO_Functions_Loader
{
    private $options;

    public function __construct()
    {
        $this->options = get_option('useful_seo_functions', array());
        add_action('init', array($this, 'load_seo_functions'));
    }

    public function load_seo_functions()
    {
        $dir = plugin_dir_path(__FILE__) . 'seo-functions/';
        $functions = [];

        foreach (scandir($dir) as $file) {
            if ('.php' === substr($file, -4)) {
                $func_name = str_replace('.php', '', $file);
                require_once $dir . $file;

                if (function_exists($func_name)) {
                    $function_info = call_user_func($func_name);
                    $priority = $function_info['priority'] ?? 10;
                    $functions[$func_name] = ['priority' => $priority, 'info' => $function_info];
                }
            }
        }

        asort($functions);

        foreach ($functions as $func_name => $func_data) {
            $applyFunction = true;
            if (isset($func_data['info']['inputs'])) {
                foreach ($func_data['info']['inputs'] as $input) {
                    if ($input['type'] === 'checkbox' && (!isset($this->options[$func_name]['enabled']) || $this->options[$func_name]['enabled'] != '1')) {
                        $applyFunction = false;
                        break;
                    }
                }
            }
            if ($applyFunction) {
                call_user_func("apply_$func_name");
            }
        }
    }
}
