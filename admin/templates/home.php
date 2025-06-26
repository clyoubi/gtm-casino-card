
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
</div>