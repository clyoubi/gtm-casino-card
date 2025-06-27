<?php

/**
 * Plugin Name: Casino Card GTM
 * Description: This plugin provides a custom shortcode that displays detailed information about a casino in a predefined, visually styled card format.
 * Version: 1.1.8
 * Author: Cedric Liam Youbi
 * Author URI: https://github.com/clyoubi
 * Text Domain: gtm-casino-card
 * Domain Path: /languages
 * Requires at least: 6.1
 * Requires PHP: 8.2
 * Update URI: https://github.com/clyoubi/gtm-casino-card
 * License: GPLv2
 * Licence URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


defined('ABSPATH') || exit;

define('GTM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GTM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GTM_REST_API_ENDPOINT', "https://2025q2wpdev.cartable.info");
define('GTM_SECRET_IV', 'FI/^zchTgou>@gI#');
define('GTM_PLUGIN_BASENAME', plugin_basename(__FILE__));
require_once GTM_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';
require_once GTM_PLUGIN_DIR . 'admin/admin.php';
require_once GTM_PLUGIN_DIR . 'admin/CacheHandler.php';
require_once GTM_PLUGIN_DIR . 'admin/shortcodes/shortcode.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;



class GTMCasinoCard
{
    public function __construct()
    {
        $this->autoload_subfolder_classes();
        (new GTMAdmin())->init();
        (new GTMCacheHandler())->init();
        (new GTMCasinoCardShortCode());
        $this->pluginUpdatesHandler();
        GTM_Setting::migrateExistingData('casino_card');
    }

    public function pluginUpdatesHandler()
    {
        $myUpdateChecker = PucFactory::buildUpdateChecker(
            'https://github.com/clyoubi/gtm-casino-card/',
            __FILE__,
            'gtm-casino-card'
        );

        $myUpdateChecker->getVcsApi()->enableReleaseAssets();
    }
    function autoload_subfolder_classes(): void
    {

            $directory = __DIR__ . "/admin/";
            if (!is_dir($directory)) {
                return; // Prevent errors if the directory doesn't exist
            }
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                $filePath = $file->getPathname();

                // Explode the path and check if any directory is named 'templates'
                $pathParts = explode(DIRECTORY_SEPARATOR, $file->getPath());

                if (
                    $file->isFile() &&
                    $file->getExtension() === 'php' &&
                    !in_array('templates', $pathParts, true)
                ) {
                    require_once $filePath;
                }
            }
        
    }
}

new GTMCasinoCard();
