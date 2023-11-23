<?php

// Ensure WordPress is running to prevent direct access
defined('ABSPATH') || exit;


class SERPCraft_Autoposting
{
    private $generated_password = '';

    public function __construct()
    {
        $this->handle_form_submission();
        wp_enqueue_script('serpcraft_admin_js', USEFUL_SEO_FUNCTIONS_PLUGIN_URL . 'public/js/serpcraft.js', array(), '1.0', true);
    }

    private function handle_form_submission()
    {
        if (isset($_POST['user_id'], $_POST['app_name']) && check_admin_referer('generate_app_password_nonce', '_wpnonce_generate_app_password')) {
            $user_id = intval($_POST['user_id']);
            $user = get_user_by('ID', $user_id);
            $app_name = sanitize_text_field($_POST['app_name']);

            $new_password_data = WP_Application_Passwords::create_new_application_password($user_id, array('name' => $app_name));

            if (is_wp_error($new_password_data)) {
                $this->generated_password = '<div class="error-card"><p><strong>Error generating password: ' . esc_html($new_password_data->get_error_message()) . '</strong></p></div>';
            } else {
                $encoded_username = base64_encode($user->user_login);
                $formatted_password = $this->base64url_encode($encoded_username . '*' . $new_password_data[0]);
                $this->generated_password = '<div class="app-password-result">
                    <h3><strong>Password for ' . esc_html($user->user_login) . ' on ' . esc_html($app_name) . ':</strong></h3>
                    <h2 id="generatedPassword">' . esc_html($formatted_password) . '</h2>
                    <p class="password-warning">Warning: Save this password immediately. It cannot be retrieved again!</p>
                    <button id="copyButton" class="button button-secondary">Copy to Clipboard</button>
                </div>';
            }
        }
    }

    public function display_form()
    {
        echo $this->generated_password;
?>

        <div class="app-password-container">
            <h2 style="margin-top:0px;"><a href="https://serpcraft.com" target="_blank">SERPcraft.com Autoposting</a></h2>
            <form method="post" action="">
                <?php wp_nonce_field('generate_app_password_nonce', '_wpnonce_generate_app_password'); ?>

                <label for="user_id">Select User:</label>
                <select class="usf_select" name="user_id" id="user_id">
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

        <!-- Display generated password below the card form -->
        <?= $this->generated_password ?>
<?php
    }

    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

