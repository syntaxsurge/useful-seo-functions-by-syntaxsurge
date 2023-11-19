<?php

// Ensure Wordpress is running to prevent direct access
defined('ABSPATH') || exit;

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Handles admin settings page rendering and saving settings

class SEO_Settings
{
    private $options;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'), 1000); // Enqueue styles

        // Suppressing other admin notices
        add_action('admin_head', array($this, 'suppress_admin_notices'));
    }

    public function suppress_admin_notices()
    {
        $screen = get_current_screen();
        if (strpos($screen->base, 'seo-functions') !== false) {
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        }
    }

    public function enqueue_admin_styles()
    {
        // Only load styles on your plugin's settings page
        $screen = get_current_screen();
        if (strpos($screen->base, 'seo-functions') !== false) {
            wp_enqueue_style('seo_functions_admin_style', USEFUL_SEO_FUNCTIONS_PLUGIN_URL . 'public/css/seo-admin.css', array(), '2.3'); // Versioning
        }
    }

    public function add_plugin_page()
    {
        add_options_page('Useful SEO Functions', 'Useful SEO Functions', 'manage_options', 'seo-functions', array($this, 'create_admin_page'));
    }

    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function export_plugin_as_zip()
    {

        // Get the version number of plugin
        if (!function_exists('get_plugin_data')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $plugin_data = get_plugin_data(plugin_dir_path(__DIR__) . 'useful-seo-functions-by-syntaxsurge.php');
        $version = $plugin_data['Version'];

        $plugin_dir = plugin_dir_path(__DIR__);
        $plugin_dir_name = basename($plugin_dir);

        // Regular expression pattern to match version numbers like v1.0.1
        $version_pattern = '/-v\d+\.\d+\.\d+/';

        // Check if the directory name already contains a version number
        if (preg_match($version_pattern, $plugin_dir_name)) {
            // Replace existing version number
            $plugin_dir_name = preg_replace($version_pattern, '-v' . $version, $plugin_dir_name);
        } else {
            // Append new version number
            $plugin_dir_name .= '-v' . $version;
        }

        $zip_file_name = $plugin_dir_name . '.zip';
        $zip_file = sys_get_temp_dir() . '/' . $zip_file_name;

        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($plugin_dir),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        // Ensure the correct relative path is calculated
                        $relativePath = substr($filePath, strlen($plugin_dir));

                        // Add file to zip
                        $zip->addFile($filePath, $relativePath);
                    }
                }

                $zip->close();

                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($zip_file) . '"');
                readfile($zip_file);
                unlink($zip_file);
                exit;
            } else {
                wp_die('Failed to create zip file');
            }
        } else {
            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';

            $archive = new PclZip($zip_file);
            $archive->create($plugin_dir, PCLZIP_OPT_REMOVE_PATH, plugin_dir_path(__DIR__));

            if ($archive->error_code != 0) {
                wp_die('Failed to create zip file: ' . $archive->errorInfo(true));
            } else {
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($zip_file) . '"');
                readfile($zip_file);
                unlink($zip_file);
                exit;
            }
        }
    }

    public function create_admin_page()
    {
        $this->options = get_option('useful_seo_functions', array());
?>
        <div class="wrap">
            <h1 class="text-center text-break">Useful SEO Functions by SyntaxSurge</h1>
            <div class="container mt-5 d-flex justify-content-center align-items-center vh-100">
                <div class="card border-0 shadow-lg rounded-lg">
                    <div class="card-body">
                        <h2 class="card-title text-muted mb-4">🛠 Need More Features?</h2>
                        <p class="card-text mb-4" style="font-size: 18px; line-height: 1.7;">Whether you're in need of a custom WordPress plugin, specialized or custom software/scripts, or any technological assistance, remember: <strong>We're here to help</strong>.</p>
                        <div class="pt-4">
                            <p class="card-text mb-2" style="font-size: 16px;">📧 Reach out to us:
                                <a href="mailto:syntaxsurge@gmail.com" class="text-info fs-5 fw-bold">syntaxsurge@gmail.com</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $generated_password = '';
            if (isset($_POST['user_id'], $_POST['app_name']) && check_admin_referer('generate_app_password_nonce', '_wpnonce_generate_app_password')) {
                $user_id = intval($_POST['user_id']);
                $user = get_user_by('ID', $user_id);
                $app_name = sanitize_text_field($_POST['app_name']);

                $new_password_data = WP_Application_Passwords::create_new_application_password($user_id, array('name' => $app_name));

                if (is_wp_error($new_password_data)) {
                    $generated_password .= '<div class="error-card"><p><strong>Error generating password: ' . esc_html($new_password_data->get_error_message()) . '</strong></p></div>';
                } else {
                    $encoded_username = base64_encode($user->user_login);
                    $formatted_password = $this->base64url_encode($encoded_username . '*' . $new_password_data[0]);
                    $generated_password .= '<div class="app-password-result">
								<p><strong><h3>Password for ' . esc_html($user->user_login) . ' on ' . esc_html($app_name) . ':</h3></strong></p>
								<h2 id="generatedPassword">' . esc_html($formatted_password) . '</h2>
								<p style="color: red;">Warning: Save this password immediately. It cannot be retrieved again!</p>
								<button id="copyButton" class="button button-secondary">Copy to Clipboard</button>
							</div>';
                }
            }
            ?>

            <!-- Display generated password above the card form -->
            <?= $generated_password ?>

            <div class="app-password-container">
                <h2 style="margin-top:0px;"><a href="https://serpcraft.com" target="_blank">SERPcraft.com Autoposting</a></h2>
                <form method="post" action="">
                    <?php wp_nonce_field('generate_app_password_nonce', '_wpnonce_generate_app_password'); ?>

                    <label for="user_id">Select User:</label>
                    <select name="user_id" id="user_id">
                        <option value="">-- Select User --</option>
                        <?php
                        $users = get_users();
                        foreach ($users as $user) {
                            echo '<option value="' . esc_attr($user->ID) . '">' . esc_html($user->display_name) . '</option>';
                        }
                        ?>
                    </select>

                    <label for="app_name">Application Name:</label>
                    <input type="text" name="app_name" id="app_name" required>

                    <input type="submit" value="Generate Password" class="button button-primary">
                </form>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const copyButton = document.getElementById('copyButton');
                    const generatedPassword = document.getElementById('generatedPassword');

                    copyButton.addEventListener('click', function() {
                        const textArea = document.createElement('textarea');
                        textArea.value = generatedPassword.textContent;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                        alert('Password copied to clipboard!');
                    });
                });
            </script>

            <!-- Display generated password below the card form -->
            <?= $generated_password ?>


            <form method="post" action="options.php" style="margin-top: 25px;">
                <?php
                settings_fields('seo_functions_group');
                // Iterate through sections and apply .settings-section class
                global $wp_settings_sections;
                if (isset($wp_settings_sections['seo-functions'])) {
                    foreach ((array) $wp_settings_sections['seo-functions'] as $section) {
                        echo '<div class="settings-section">';
                        if ($section['title']) {
                            echo "<h2>{$section['title']}</h2>\n";
                        }
                        if ($section['callback']) {
                            call_user_func($section['callback'], $section);
                        }
                        echo '<table class="form-table" role="presentation">';
                        do_settings_fields('seo-functions', $section['id']);
                        echo '</table></div>';
                    }
                }
                submit_button();
                ?>
            </form>

            <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                <input type="hidden" name="action" value="export_plugin_zip">
                <button type="submit" class="export-zip-button">Export Plugin as ZIP</button>
            </form>
        </div>
<?php
    }

    public function page_init()
    {
        add_action('admin_post_export_plugin_zip', array($this, 'export_plugin_as_zip'));
        register_setting('seo_functions_group', 'useful_seo_functions', array($this, 'sanitize'));

        $dir = plugin_dir_path(__FILE__) . 'seo-functions/';
        $categories = [];

        // Iterate through function files and collect categories
        foreach (scandir($dir) as $file) {
            if ('.php' === substr($file, -4)) {
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
            add_settings_section($category_name, $category_name, null, 'seo-functions');

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

    public function sanitize($input)
    {
        $new_input = [];
        foreach ($input as $func_name => $settings) {
            foreach ($settings as $key => $value) {
                // You may switch on $key or define type in $function_info and switch on it
                $new_input[$func_name][$key] = sanitize_text_field($value);
            }
        }
        return $new_input;
    }

    public function field_callback($args)
    {
        $func_name = $args['func_name'];
        $description = $args['description'];
        $inputs = $args['inputs'];
        $options = $this->options[$func_name] ?? [];

        // Wrap fields with .settings-field class
        echo '<div class="settings-field">';

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

        echo '</div>';
    }
}
