<?php

/**
 * Plugin Name: Casino Card GTM
 * Description: This plugin provides a custom shortcode that displays detailed information about a casino in a predefined, visually styled card format.
 * Version: 1.1.6
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
require_once GTM_PLUGIN_DIR . 'admin/includes/models/setting.php';
require_once GTM_PLUGIN_DIR . 'admin/shortcodes/CasinoCard/CasinoCard.php';
require_once GTM_PLUGIN_DIR . 'admin/admin.php';
require_once GTM_PLUGIN_DIR . 'admin/CacheHandler.php';
require_once GTM_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class GTMCasinoCard
{
    public function __construct()
    {
        (new GTMAdmin())->init();
        (new GTMCacheHandler())->init();
        (new GTMCasinoCardShortCode())->init();
        $this->pluginUpdatesHandler();
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
}


new GTMCasinoCard();
