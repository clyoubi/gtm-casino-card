<?php
defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'models/casino.php';

class GTMCasinoCardShortCode
{
    public function init()
    {
        add_action('init', [$this, 'register_shortcode_if_safe']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_casino_shortcode_css']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_shortcode_if_safe()
    {
        if (shortcode_exists('casino_card')) {
            // Conflict detected: show admin warning and deactivate plugin
            if (is_admin() && current_user_can('activate_plugins')) {
                deactivate_plugins(GTM_PLUGIN_BASENAME);

                add_action('admin_notices', function () {
                    echo '<div class="notice notice-error is-dismissible">';
                    echo '<p><strong>GTM Casino Card:</strong> Plugin deactivated because the shortcode <code>[casino_card]</code> is already registered by another plugin or theme.</p>';
                    echo '</div>';
                });
            }
        } else {
            // No conflict: register the shortcode
            add_shortcode('casino_card', [$this, 'render_cards_shortcode']);
        }
    }

    function enqueue_casino_shortcode_css()
    {
        if (is_singular() && has_shortcode(get_post()->post_content, 'casino_card')) {
            wp_enqueue_style(
                'gtm-casino-card-style',
                GTM_PLUGIN_URL . 'assets/css/style.css',
                [],
                '1.0.0'
            );

            if (get_option("casino_general_dark_mode") === 'yes') {
                wp_enqueue_style(
                    'gtm-casino-card-style-dark',
                    GTM_PLUGIN_URL . 'assets/css/darkmode.css',
                    [],
                    '1.0.0'
                );
            }
        }
    }


    public function render_cards_shortcode($atts)
    {
        $atts = shortcode_atts([
            'id'                     => '',
            'header_color'           => get_option('casino_general_logo_background', '#000'),
            'cta_color'              => get_option('casino_general_cta_color', "#287e29"),
            'go'                     => '#',
            'auto_dark_mode'         => get_option('casino_general_dark_mode', 'no'),
            'display_brand_name'     => get_option('casino_general_logo_type', 'no'),
        ], $atts, 'casino_card');


        $ID = $atts['id'];
        $BG_COLOR =  $atts['header_color'];
        $CTA_COLOR =  $atts['cta_color'];
        $DARK_MODE =  $atts['auto_dark_mode'];
        $DISPLAY_BRAND_NAME = $atts['display_brand_name'];
        $go = $atts['go'];

        $cache_key = 'casino_card_cache_' . md5($ID);
        $use_cache = get_option('casino_general_enable_cache') === 'yes';
        $cache_duration = (int) get_option('casino_cache_delay', 1) * HOUR_IN_SECONDS;


        if ($ID === '' && get_option("casino_general_fetch_all_casinos") !== 'yes') {
            return 'Casino ID not set';
        }

        if ($DARK_MODE === 'yes') {
            wp_enqueue_style(
                'gtm-casino-card-style-dark',
                GTM_PLUGIN_URL . 'assets/css/darkmode.css',
                [],
                '1.0.0'
            );
        }

        // Try to load from cache if enabled
        if ($use_cache) {
            $cached = get_transient($cache_key);
            if ($cached !== false) {
                $casinos = $cached;
                foreach ($casinos as $item) {
                    if (filter_var($go, FILTER_VALIDATE_URL)) {
                        $item->go = $go;
                    }
                }
                ob_start();
                include GTM_PLUGIN_DIR . 'templates/CasinoCard.php';
                return ob_get_clean();
            }
        }

        // Build request
        $url = GTM_REST_API_ENDPOINT . "/casinos/$ID";
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
        if (is_wp_error($response)) return 'Failed to load data.';

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 200) return 'Bad credentials.';

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data)) return 'Invalid data received.';

        $casinos = [];

        if (isset($data[0]) && is_array($data[0])) {
            foreach ($data as $item) {
                $casino = new Casino($item);
                if (filter_var($go, FILTER_VALIDATE_URL)) {
                    $casino->go = $go;
                }
                array_push($casinos, $casino);
            }
        } else {
            $casino = new Casino($data);
            if (filter_var($go, FILTER_VALIDATE_URL)) {
                $casino->go = $go;
            }
            array_push($casinos, $casino);
        }

        // Save to cache if enabled
        if ($use_cache) {
            set_transient($cache_key, $casinos, $cache_duration);
            GTMCacheHandler::add_cache_key($cache_key);
        }

        ob_start();
        include GTM_PLUGIN_DIR . 'templates/CasinoCard.php';
        return ob_get_clean();
    }

    public function register_settings()
    {

        add_settings_section(
            'gtm_api_section',
            _x('API Settings', 'gtm-casino-card'),
            fn() => print('<p>Configure your casino API credentials.</p>'),
            'casino_card_settings'
        );

        (new GTM_Setting(
            'casino_api_username',
            _x('API Username', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_api_section',
            'text',
            _x('Your API username', 'gtm-casino-card'),
            null
        ))->create();

        (new GTM_Setting(
            'casino_api_password',
            _x('API Password', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_api_section',
            'password',
            _x('Your API password', 'gtm-casino-card'),
            null
        ))->create();


        add_settings_section(
            'gtm_general_section',
            _x('General Settings', 'gtm-casino-card'),
            fn() => print('<p>Configure your casino shortcode.</p>'),
            'casino_card_settings'
        );

        (new GTM_Setting(
            'casino_general_currency',
            _x('Currency For Bonus', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_general_section',
            'select',
            _x('Currency to use to display the bonuses', 'gtm-casino-card'),
            'EUR',
            Casino::$currencies
        ))->create();

        (new GTM_Setting(
            'casino_general_logo_type',
            _x('Caisno header logo', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_general_section',
            'checkbox',
            _x('Display the Brand name along side the logo of the logo only, this option can be overrinden within the shortcode itself', 'gtm-casino-card')
        ));

        (new GTM_Setting(
            'casino_general_enable_cache',
            _x('Caisno Enable Cache', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_general_section',
            'checkbox',
            _x('Enable cache to better load performance', 'gtm-casino-card'),
        ))->create();

        (new GTM_Setting(
            'casino_cache_delay',
            _x('Caisno cache duration (hours)', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_general_section',
            'number',
            _x('Number in hours to keep data locally, the new request will be perform every x hours', 'gtm-casino-card'),
            1
        ))->create();

        (new GTM_Setting(
            'casino_general_fetch_all_casinos',
            _x('Display all casinos', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_general_section',
            'checkbox',
            _x('Display all casinos if not id is set in the shortcode', 'gtm-casino-card')
        ))->create();

        add_settings_section(
            'gtm_shortcode_section',
            'Shortcode General Settings',
            fn() => print('<p>Configure your casino shortcode.</p>'),
            'casino_card_settings'
        );

        (new GTM_Setting(
            'casino_general_dark_mode',
            _x('Casino Card Dark Mode', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_shortcode_section',
            'checkbox',
            _x('Enable Dark Mode so the Card matches the user browser UI mode configuration as a black card', 'gtm-casino-card')
        ))->create();

        (new GTM_Setting(
            'casino_general_logo_type',
            _x('Caisno header logo', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_shortcode_section',
            'checkbox',
            _x('Display the Brand name along side the logo of the logo only, this option can be overrinden within the shortcode itself', 'gtm-casino-card')
        ))->create();

        (new GTM_Setting(
            'casino_general_logo_background',
            _x('Caisno header background color', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_shortcode_section',
            'color',
            _x('Logo default background color', 'gtm-casino-card'),
            "#000000"
        ))->create();

        (new GTM_Setting(
            'casino_general_cta_color',
            _x('Caisno CTA background color', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_shortcode_section',
            'color',
            _x('Call to action default background color', 'gtm-casino-card'),
            "#287e29",
        ))->create();
    }
}
