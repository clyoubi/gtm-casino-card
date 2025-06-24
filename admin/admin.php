<?php

class GTMAdmin
{
    public static string $GTM_API_USERNAME;
    public static string $GTM_API_PASSWORD;
    public function init()
    {
        add_action('admin_init', [$this, 'clear_casino_card_cache_request']);
        add_action('admin_init', [$this, 'test_connection']);
        add_action('admin_menu', [$this, 'casino_card_plugin_menu']);
        // Enqueue custom admin css to admin panel only if the admin home page is visited
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_board_css']);
        register_activation_hook(__FILE__, [$this, 'generate_encryption_keys_if_needed']);
        register_deactivation_hook(__FILE__, [$this, 'plugin_deactivation_notify']);
    }

    public static function autoload_subfolder_classes(): void
    {
        $dirs = glob(__DIR__ . '/shortcodes/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            foreach (glob($dir . '/*.php') as $file) {
                require_once $file;
            }
        }
    }


    public static function getAPICredentials($value): string
    {
        switch ($value) {
            case "username":
                return sanitize_text_field(get_option('casino_api_username'));
                break;
            case 'password':
                return sanitize_text_field(GTMAdmin::gtm_decrypt(get_option('casino_api_password')));
                break;
        }
        return '';
    }
    public function test_connection()
    {
        if (
            isset($_POST['action']) &&
            $_POST['action'] === 'test_api_connection_creds' &&
            check_admin_referer('test_api_connection_creds', 'test_api_connection_creds_nonce')
        ) {

            $url = GTM_REST_API_ENDPOINT . "/casinos";
            $username = GTMAdmin::getAPICredentials('username');
            $password = GTMAdmin::getAPICredentials('password');

            if (empty($username) || empty($password)) {
                return 'API not configured.';
            }

            $args = [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
                ],
            ];

            $response = wp_remote_get($url, $args);
            $code = wp_remote_retrieve_response_code($response);
            if (is_wp_error($response)) {
                printf(
                    '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
                    esc_html_x(
                        'An error occurred during the verification. Double check your Internet connection and try again later.',
                        'Error message in admin notice',
                        'gtm-casino-card'
                    )
                );
            } else {
                if ($code == 200) {
                    return add_action('admin_notices', function () {
                        printf(
                            '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
                            esc_html_x('API Keys are correct!', 'Success message after API key validation', 'gtm-casino-card')
                        );
                    });
                } else {
                    return add_action('admin_notices', function () {
                        printf(
                            '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
                            esc_html_x('Bad Credentials', 'Error message for invalid API credentials', 'gtm-casino-card')
                        );
                    });
                }
            }
        }
    }

    public static function clear_casino_card_cache_request()
    {
        if (
            isset($_POST['action']) &&
            $_POST['action'] === 'casino_card_cache' &&
            check_admin_referer('clear_casino_card_cache', 'clear_casino_card_cache_nonce')
        ) {
            GTMCacheHandler::clear_casino_card_cache();
        }
    }

    public function casino_card_plugin_menu()
    {
        add_menu_page(
            __('Casino Card Shortcode Handler', 'gtm-casino-card'),
            __('Casino Card', 'gtm-casino-card'),
            'edit_posts',
            'gtm-casino-card',
            [$this, 'gtm_casino_card_page_html'],
            'dashicons-games',
            6
        );

        add_submenu_page(
            'gtm-casino-card',
            __('Settings page', 'gtm-casino-card'),
            __('Settings', 'gtm-casino-card'),
            'manage_options',
            'casino_card_settings',
            [$this, 'gtm_casino_card_page_settings_page_html']
        );
    }

    public function gtm_casino_card_page_settings_page_html()
    {
        // Making sure contriubutors can access to read the documentation
        if (!current_user_can('manage_options')) {
            return;
        }
        include GTM_PLUGIN_DIR . 'admin/templates/settings.php';
    }

    public function gtm_casino_card_page_html()
    {
        // Making sure only author role and above can access to the setting page
        if (!current_user_can('edit_posts')) {
            return;
        }
        include GTM_PLUGIN_DIR . 'admin/templates/home.php';
    }

    function plugin_deactivation_notify()
    {
        $admin_emails = [];

        // Get users with 'administrator' role
        $admins = get_users(['role' => 'administrator']);
        foreach ($admins as $admin) {
            $admin_emails[] = $admin->user_email;
        }

        $subject = 'Plugin Deactivated: GTM Casino Card';
        $message = "The plugin *GTM Casino Card* was just deactivated on " . get_bloginfo('name') . " (" . home_url() . ").";

        wp_mail($admin_emails, $subject, $message);
    }

    public function enqueue_admin_board_css()
    {
        $current_screen = get_current_screen();
        if (strpos($current_screen->base, 'gtm-casino-card') === false) {
            return;
        } else {
            wp_enqueue_style('boot_css', GTM_PLUGIN_URL . 'assets/css/admin-home.css', [], '1.0.0');
        }
    }

    public function generate_encryption_keys_if_needed()
    {
        if (!get_option('gtm_casino_api_encryption_key')) {
            update_option('gtm_casino_api_encryption_key', wp_generate_password(32, false));
        }
    }


    public static function get_encryption_key()
    {
        return get_option('gtm_casino_api_encryption_key');
    }

    public static function get_encryption_iv()
    {
        return GTM_SECRET_IV;
    }

    public static function gtm_encrypt($data)
    {
        return base64_encode(openssl_encrypt(
            $data,
            'AES-256-CBC',
            self::get_encryption_key(),
            0,
            self::get_encryption_iv()
        ));
    }

    public static function gtm_decrypt($data)
    {
        return openssl_decrypt(
            base64_decode($data),
            'AES-256-CBC',
            self::get_encryption_key(),
            0,
            self::get_encryption_iv()
        );
    }
}
