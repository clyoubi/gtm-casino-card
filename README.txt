=== Casino Card GTM ===
Contributors: clyoubi
Tags: casino, marketing
Requires at least: 6.1
Tested up to: 6.8
Stable tag: v1.1.6
Requires PHP: 8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
This plugin provides a custom shortcode that displays detailed information about a casino in a predefined, visually styled card format.

== Description ==

The GTM Casino Card plugin allows you to display casino listings in a stylish card layout anywhere on your site using a shortcode. Data is fetched from an external API with optional caching and customization.

**Features:**
- Display individual or multiple casino cards
- Configure API credentials and settings in admin panel
- Enable transient caching with duration control
- Customizable header and CTA background colors
- Optional dark mode and brand name display

== Installation ==

1. Download the plugin `.zip` file.
2. In your WordPress dashboard, go to **Plugins > Add New > Upload Plugin**.
3. Upload the `.zip` file and activate the plugin.
4. Navigate to **Settings > Casino Card** to configure your options.

== Usage ==

Use the following shortcode to display a casino card:

`[casino_card id="123" header_color="#000" auto_dark_mode="yes" cta_color="#287e29" go="https://example.com"]`

== Frequently Asked Questions ==

= How do I get my API credentials? =
You must register with the casino data provider and obtain your API username and password.

= Can I customize the card design? =
Yes! You can control colors, dark mode, and whether to show the brand name from the plugin settings.

== Screenshots ==

1. Casino card example in the frontend.
2. Plugin settings page in the admin panel.

== Changelog ==

= 1.1.7 =

* Improved cache logic and shortcode response structure.
* Separing, card rendering with shortcode building function
* Better cache management, avoiding caching unsuccessfull API responses
* Clear cache on save options
* Add CHANGELOG.md and update README.txt

= 1.0.2 =
* Improved cache logic and shortcode response structure.

= 1.0.1 =
* Added GitHub deployment support and Telegram release notifications.

= 1.0.0 =
* Initial release with core features.
