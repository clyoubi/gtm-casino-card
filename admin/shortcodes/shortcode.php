<?php
interface IGTM_ShortCode
{
    public function build_shortcode(array $atts);
    public function register_settings();
    public function define();
}

abstract class GTM_ShortCode implements IGTM_ShortCode
{

    public string $id;
    public string $name;
    public string $description;
    public string $settings_slug;

    public function __construct()
    {
        $this->define();
        $this->init();
    }

    public function define(): void {}

    protected function init(): void
    {
        add_action('init', [$this, 'register_shortcode_if_safe']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_init', [$this, 'register_global_settings']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'register_shortcode_menu_item']);
        $this->settings_slug = "gtm_$this->id". "_shortcode_settings";
    }

    public function register_shortcode_menu_item()
    {
        add_submenu_page(
            'gtm-casino-card',
            __($this->description, 'gtm-casino-card'),
            __($this->name, 'gtm-casino-card'),
            'edit_posts',
            "gtm_$this->id" . "_shortcode",
            [$this, 'render_html_page'],
            1
        );
    }

    public function render_html_page()
    {
        $tabs = [];

        if (current_user_can('edit_posts')) {
            $tabs['general'] = __('General', 'gtm-casino-card');
        }

        if (current_user_can('manage_options')) {
            $tabs['settings'] = __('Settings', 'gtm-casino-card');
        }

        // Get current tab from query string or default to first visible tab
        $current_tab = isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs)
            ? sanitize_text_field($_GET['tab'])
            : array_key_first($tabs);

        echo '<div class="wrap">';
        echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';

        // Render the tab navigation
        echo '<nav class="nav-tab-wrapper">';
        foreach ($tabs as $tab_key => $tab_label) {
            $active = ($tab_key === $current_tab) ? ' nav-tab-active' : '';
            $url = admin_url('admin.php?page=gtm_' . $this->id . '_shortcode&tab=' . $tab_key);
            echo '<a href="' . esc_url($url) . '" class="nav-tab' . esc_attr($active) . '">' . esc_html($tab_label) . '</a>';
        }
        echo '</nav>';

        // Render the content of the selected tab
        echo '<div class="tab-content">';
        switch ($current_tab) {
            case 'general':
                $this->render_general_tab();
                break;
            case 'settings':
                if (current_user_can('manage_options')) {
                    $this->render_settings_tab();
                } else {
                    echo '<p>' . esc_html__('You do not have permission to view this tab.', 'gtm-casino-card') . '</p>';
                }
                break;
        }
        echo '</div>';
        echo '</div>';
    }

    protected function render_general_tab()
    {
        $className = $this->class_name();
        include __DIR__ ."/$className/templates/admin.php";
    }

    protected function render_settings_tab()
    {
        echo '<form method="post" action="options.php">';
        settings_fields($this->settings_slug);
        do_settings_sections($this->settings_slug);
        submit_button(__('Save Settings', 'gtm-casino-card'));
        echo '</form>';
    }



    public function register_shortcode_if_safe()
    {
        if (shortcode_exists($this->id)) {
            if (is_admin() && current_user_can('activate_plugins')) {
                deactivate_plugins(GTM_PLUGIN_BASENAME);

                add_action('admin_notices', function () {
                    printf(
                        '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
                        esc_html(sprintf(
                            __('GTM Casino Card: Shortcode [%s] is already registered.', 'gtm-casino-card'),
                            $this->id
                        ))
                    );
                });
            }
            return;
        }

        add_shortcode($this->id, function ($atts = [], $content = '') {
            $data = $this->build_shortcode($atts);
            if ($data['status'] === true) {
                return $this->render_shortcode($data);
            } else {
                printf(
                    '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
                    esc_html(sprintf(
                        __("We cannot display the shortcode at the moment", 'gtm-casino-card'),
                        $this->id
                    ))
                );
            }
        });
    }

    protected function render_shortcode(mixed $data): string
    {
        ob_start();
        $className = $this->class_name();
        $template = __DIR__ . "/$className/templates/$className.php";

        if (file_exists($template)) {
            include $template;
        } else {
            echo "<p>Template <code>shortcode-{$className}.php</code> not found.</p>";
        }

        return ob_get_clean();
    }

    public function enqueue_assets(): void
    {
        if (is_singular() && has_shortcode(get_post()->post_content, $this->id)) {
            wp_enqueue_style(
                'gtm-' . $this->id . '-style',
                plugin_dir_url(__FILE__) . $this->class_name() . '/public/css/style.css',
                [],
                '1.0.0'
            );
            wp_enqueue_script(
                'gtm-' . $this->id . '-script',
                plugin_dir_url(__FILE__) . $this->class_name() . '/public/js/js.js',
                [],
                '1.0.0',
                true
            );
        }
    }

    protected function class_name(): string
    {
        $fullClass = (new \ReflectionClass($this))->getShortName();
        return preg_replace('/^GTM(.*?)ShortCode$/', '$1', $fullClass);
    }

    public function register_global_settings() {
        add_settings_section(
            'gtm_api_section',
            __('API Settings', 'gtm-casino-card'),
            fn() => print('<p>Configure your casino API credentials.</p>'),
            'casino_card_settings'
        );

        (new GTM_Setting(
            'casino_api_username',
            __('API Username', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_api_section',
            'text',
            __('Your API username', 'gtm-casino-card'),
            null
        ))->create();

        (new GTM_Setting(
            'casino_api_password',
            __('API Password', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_api_section',
            'password',
            __('Your API password', 'gtm-casino-card'),
            null
        ))->create();

        (new GTM_Setting(
            'casino_general_enable_cache',
            __('Caisno Enable Cache', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_api_section',
            'checkbox',
            __('Enable cache to better load performance', 'gtm-casino-card'),
        ))->create();

        (new GTM_Setting(
            'casino_cache_delay',
            __('Caisno cache duration (hours)', 'gtm-casino-card'),
            'casino_card_settings',
            'gtm_api_section',
            'number',
            __('Number in hours to keep data locally, the new request will be perform every x hours', 'gtm-casino-card'),
            1
        ))->create();

    }

    public function register_settings() {}
}
