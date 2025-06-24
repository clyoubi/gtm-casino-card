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
        add_action('admin_init', [$this, 'register_settings']);
        $this->autoload_subfolder_classes();
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
        // Register shortcode
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
        $template = GTM_PLUGIN_DIR . "templates/$className.php";

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

    public static function autoload_subfolder_classes(): void
    {
        $dirs = glob(__DIR__ . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            foreach (glob($dir . '/*.php') as $file) {
                require_once $file;
                echo "$file <br>";
            }
        }
    }

    public function register_settings() {}
}
