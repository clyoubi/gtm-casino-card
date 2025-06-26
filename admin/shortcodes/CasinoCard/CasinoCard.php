<?php
defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'models/casino.php';

class GTMCasinoCardShortCode extends GTM_ShortCode
{

    public function define(): void
    {
        $this->id = 'casino_card';
        $this->name = 'Casino Card';
        $this->description = 'Display Casino Cards';
    }
    
    public function enqueue_casino_shortcode_css()
    {
        parent::enqueue_assets();
        if (GTM_Setting::getSetting('casino_card',"casino_general_dark_mode") === 'yes') {
            wp_enqueue_style(
                'gtm-casino-card-style-dark',
                GTM_PLUGIN_URL . 'assets/css/darkmode.css',
                [],
                '1.0.0'
            );
        }
    }


    public function build_shortcode($atts)
    {
        $atts = shortcode_atts([
            'id'                  => '',
            'header_color'        => GTM_Setting::getSetting('casino_card', 'casino_general_logo_background', '#000'),
            'cta_color'           =>GTM_Setting::getSetting('casino_card','casino_general_cta_color', "#287e29"),
            'go'                  => '#',
            'auto_dark_mode'      => GTM_Setting::getSetting('casino_card','casino_general_dark_mode', 'no'),
            'display_brand_name'  => GTM_Setting::getSetting('casino_card','casino_general_logo_type', 'no'),
        ], $atts, 'casino_card');

        $ID = $atts['id'];
        $BG_COLOR = $atts['header_color'];
        $CTA_COLOR = $atts['cta_color'];
        $DARK_MODE = $atts['auto_dark_mode'];
        $DISPLAY_BRAND_NAME = $atts['display_brand_name'];
        $go = $atts['go'];

        $cache_key = 'casino_card_cache_' . md5($ID);
        $use_cache = GTM_Setting::getGlobalSettings('casino_general_enable_cache') === 'yes';
        $cache_duration = (int) GTM_Setting::getGlobalSettings('casino_cache_delay', 1) * HOUR_IN_SECONDS;

        $casinos = [];

        // Response structure
        $result = [
            'status'  => true,
            'message' => '',
            'extra'   => [
                'BG_COLOR'            => $BG_COLOR,
                'CTA_COLOR'           => $CTA_COLOR,
                'DISPLAY_BRAND_NAME'  => $DISPLAY_BRAND_NAME,
            ],
            'data'    => [],
        ];

        $username = GTMAdmin::getAPICredentials('username');
        $password = GTMAdmin::getAPICredentials('password');

        if (empty($username) || empty($password)) {
            $result['message'] = 'API not configured.';
            $result['status'] = false;
            return $result;
        }

        if ($ID === '' && GTM_Setting::getSetting('casino_card',"casino_general_fetch_all_casinos") !== 'yes') {
            $result['message'] = 'Casino ID not set.';
            $result['status'] = false;
            return $result;
        }

        if ($DARK_MODE === 'yes') {
            wp_enqueue_style(
                'gtm-casino-card-style-dark',
                GTM_PLUGIN_URL . 'assets/css/darkmode.css',
                [],
                '1.0.0'
            );
        }

        // Load from cache if possible
        if ($use_cache) {
            $cached = get_transient($cache_key);
            if ($cached !== false) {
                foreach ($cached as $casino) {
                    if (filter_var($go, FILTER_VALIDATE_URL)) {
                        $casino->go = $go;
                    }
                }
                $result['status'] = true;
                $result['message'] = 'Data loaded from cache.';
                $result['data'] = $cached;
                return $result;
            }
        }

        // Make API request
        $url = GTM_REST_API_ENDPOINT . "/casinos/$ID";
        $args = [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            ],
        ];

        $response = wp_remote_get($url, $args);
        if (is_wp_error($response)) {
            $result['message'] = 'Failed to fetch data.';
            $result['status'] = false;
            return $result;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code === 401) {
            $result['message'] = 'Bad credentials.';
            $result['status'] = false;
            return $result;
        }

        if ($code === 422) {
            $result['message'] = 'Wrong Casino ID.';
            $result['status'] = false;
            return $result;
        }

        if ($code !== 200) {
            $result['message'] = 'Something went wrong. We cannot process the shortcode at the moment';
            $result['status'] = false;
            return $result;
        }

        $body = wp_remote_retrieve_body($response);
        $raw_data = json_decode($body, true);
        if (empty($raw_data)) {
            $result['message'] = 'Invalid data received.';
            $result['status'] = false;
            return $result;
        }

        if (isset($raw_data[0]) && is_array($raw_data[0])) {
            foreach ($raw_data as $item) {
                $casino = new Casino($item);
                if (filter_var($go, FILTER_VALIDATE_URL)) {
                    $casino->go = $go;
                }
                $casinos[] = $casino;
            }
        } else {
            $casino = new Casino($raw_data);
            if (filter_var($go, FILTER_VALIDATE_URL)) {
                $casino->go = $go;
            }
            $casinos[] = $casino;
        }

        if ($use_cache && $result['status'] === true) {
            set_transient($cache_key, $casinos, $cache_duration);
            GTMCacheHandler::add_cache_key($cache_key);
        }

        $result['message'] = 'Data fetched successfully.';
        $result['data'] = $casinos;
        $result['status'] = true;

        return $result;
    }


    public function register_settings()
    {
        add_settings_section(
            'gtm_general_section',
            __('General Settings', 'gtm-casino-card'),
            fn() => print('<p>Configure your casino shortcode.</p>'),
            $this->settings_slug
        );

        (new GTM_Setting(
            'casino_general_currency',
            __('Currency For Bonus', 'gtm-casino-card'),
            $this->settings_slug,
            'gtm_general_section',
            'select',
            __('Currency to use to display the bonuses', 'gtm-casino-card'),
            'EUR',
            Casino::$currencies
        ))->create();

        (new GTM_Setting(
            'casino_general_logo_type',
            __('Caisno header logo', 'gtm-casino-card'),
            $this->settings_slug,
            'gtm_general_section',
            'checkbox',
            __('Display the Brand name along side the logo of the logo only, this option can be overrinden within the shortcode itself', 'gtm-casino-card')
        ));

        (new GTM_Setting(
            'casino_general_fetch_all_casinos',
            __('Display all casinos', 'gtm-casino-card'),
            $this->settings_slug,
            'gtm_general_section',
            'checkbox',
            __('Display all casinos if not id is set in the shortcode', 'gtm-casino-card')
        ))->create();

        add_settings_section(
            'gtm_shortcode_section',
            'Shortcode General Settings',
            fn() => print('<p>Configure your casino shortcode.</p>'),
            $this->settings_slug
        );

        (new GTM_Setting(
            'casino_general_dark_mode',
            __('Casino Card Dark Mode', 'gtm-casino-card'),
            $this->settings_slug,
            'gtm_shortcode_section',
            'checkbox',
            __('Enable Dark Mode so the Card matches the user browser UI mode configuration as a black card', 'gtm-casino-card')
        ))->create();

        (new GTM_Setting(
            'casino_general_logo_type',
            __('Caisno header logo', 'gtm-casino-card'),
            $this->settings_slug,
            'gtm_shortcode_section',
            'checkbox',
            __('Display the Brand name along side the logo of the logo only, this option can be overrinden within the shortcode itself', 'gtm-casino-card')
        ))->create();

        (new GTM_Setting(
            'casino_general_logo_background',
            __('Caisno header background color', 'gtm-casino-card'),
            $this->settings_slug,
            'gtm_shortcode_section',
            'color',
            __('Logo default background color', 'gtm-casino-card'),
            "#000000"
        ))->create();

        (new GTM_Setting(
            'casino_general_cta_color',
            __('Caisno CTA background color', 'gtm-casino-card'),
            $this->settings_slug,
            'gtm_shortcode_section',
            'color',
            __('Call to action default background color', 'gtm-casino-card'),
            "#287e29",
        ))->create();
    }
}
