<?php

class GTMCacheHandler
{
    public $cron_job_interval_name = 'gtm_casino_card_cache_data_cron_interval';
    public $cron_job_name = 'gtm_cron_clear_cache';
    public static $CACHE_DATA_KEYS = 'gtm_cache_keys';
    public function init()
    {
        add_filter('cron_schedules', [$this, 'add_custom_cron_interval']);
        add_action('admin_init', [$this, 'add_cache_key']);
        register_activation_hook(__FILE__, [$this, 'schedule_cron']);
        register_deactivation_hook(__FILE__, [$this, 'clear_cron']);
    }

    function add_custom_cron_interval($schedules)
    {
        $hours = (int) get_option('casino_cache_delay', 1);
        if ($hours < 1) $hours = 1;

        $schedules[$this->cron_job_interval_name] = [
            'interval' => $hours * HOUR_IN_SECONDS,
            'display'  => "Every $hours Hour(s)",
        ];
        return $schedules;
    }

    public static function add_cache_key($key)
    {
        $keys = get_option(self::$CACHE_DATA_KEYS, []);
        if (!in_array($key, $keys)) {
            $keys[] = $key;
            update_option(self::$CACHE_DATA_KEYS, $keys);
        }
    }

    function schedule_cron()
    {
        $use_cache = get_option('casino_general_enable_cache') === 'yes';

        if ($use_cache && !wp_next_scheduled($this->cron_job_name)) {
            wp_schedule_event(time(), $this->cron_job_interval_name, $this->cron_job_name);
        }
    }

    function clear_cron()
    {
        wp_clear_scheduled_hook($this->cron_job_name);
        $this->clear_casino_card_cache();
    }

    public static function clear_casino_card_cache()
    {
        $keys = get_option(self::$CACHE_DATA_KEYS, []);
        foreach ($keys as $key) {
            delete_transient($key);
        }
        delete_option(self::$CACHE_DATA_KEYS); // reset

        // Optional: add admin notice
        add_action('admin_notices', function () {
            echo '<div class="notice notice-success is-dismissible"><p>Casino cache cleared successfully.</p></div>';
        });
    }
}
