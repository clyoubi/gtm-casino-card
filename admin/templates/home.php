
<?php 
    $general_bg_color = get_option("casino_general_logo_background", "#000");
    $general_cta_bg_color = get_option("casino_general_cta_color", "#000");
?>
<div class="wrap">
    <h1><?php esc_html_e('GTM Casino Card Plugin', 'gtm-casino-card'); ?></h1>
    <p>
        <?php esc_html_e("This plugin provides a custom shortcode that displays detailed information about a casino in a predefined, visually styled card format.
        Each casino card is dynamically rendered with a shortcode based on a unique identifier, making it easy for content editors to insert specific casino profiles anywhere across the site.
        The plugin is designed for flexibility, seamless integration, and ease of use within the WordPress block editor or classic editor.", 'gtm-casino-card') ?>
    </p>

    <h2><?php esc_html_e('Usage', 'gtm-casino-card'); ?></h2>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Parameter</th>
                <th scope="col">Description</th>
                <th scope="col">Default value</th>
                <th scope="col">Example</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong>
                    <?php if(get_option("casino_general_fetch_all_casinos") !== 'yes') : ?> 
                        <i>(required)</i>
                    <?php endif; ?>
                </td>
                <td>id is the unique identifier for the casino. If not set, the shortcode will list and display all the Casinos</td>
                <td>N/A</td>
                <td><code>[casino_card id="3c3c3c3c-3c3c-3c3c-3c3c-3c3c3c3c3c3c"]</code></td>
            </tr>
            <tr>
                <td><strong>header_color</strong></td>
                <td>header_color defines the background color of the header where the logo is located. When defined, it overides the general color configuration set in the settings</td>
                <td style="background-color:<?php echo esc_html($general_bg_color); ?>"></td>
                <td><code>[casino_card header_color="#000"]</code></td>
            </tr>
            <tr>
                <td><strong>cta_color</strong></td>
                <td>cta_color defines the background color of the CTA button "Play now". When defined, it overides the general color configuration set in the settings</td>
                <td style="background-color:<?php echo esc_html($general_cta_bg_color); ?>"></td>
                <td><code>[casino_card cta_color="#000"]</code></td>
            </tr>
            <tr>
                <td><strong>go</strong></td>
                <td>Defines the link to redirect when the user clicks on "Play now". It will overide the API response link if defined</td>
                <td>#</td>
                <td><code>[casino_card go="https://linkedin.com/id/clyoubi"]</code></td>
            </tr>

            <tr>
                <td><strong>auto_dark_mode</strong></td>
                <td>Defines if the card should use adaptative design to dark mode automatically based on user device configuration</td>
                <td><?php echo get_option("casino_general_dark_mode", "no") ? 'yes' : 'no'; ?></td>
                <td><code>[casino_card auto_dark_mode="yes"]</code></td>
            </tr>

            <tr>
                <td><strong>display_brand_name</strong></td>
                <td>Defines if the header should display the brand name along side it's logo.</td>
                <td><?php echo get_option("casino_general_logo_type", "no") ? 'yes' : 'no'; ?></td>
                <td><code>[casino_card display_brand_name="yes"]</code></td>
            </tr>
        </tbody>
    </table>
</div>