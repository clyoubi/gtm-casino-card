<div class="wrap">
    <h1><?php esc_html_e('GTM Casino Card Settings', 'gtm-casino-card'); ?></h1>
    <form method="post" action="options.php">
        <?php
        // output security fields for the registered setting "myplugin_options"
        settings_fields('casino_card_settings');
        // output setting sections and their fields
        do_settings_sections('casino_card_settings');
        // output save settings button
        submit_button('Save Settings');
        ?>
    </form>

    <!-- Clear Cache Button -->
    <div style="display: flex; gap: 10px;">
        <form method="post">
            <?php wp_nonce_field('clear_casino_card_cache', 'clear_casino_card_cache_nonce'); ?>
            <input type="hidden" name="action" value="casino_card_cache" />
            <?php submit_button('Clear Cache Now', 'secondary'); ?>
        </form>

        <form method="post">
            <?php wp_nonce_field('test_api_connection_creds', 'test_api_connection_creds_nonce'); ?>
            <input type="hidden" name="action" value="test_api_connection_creds" />
            <?php submit_button('Test API Keys', 'secondary'); ?>
        </form>
    </div>

</div>