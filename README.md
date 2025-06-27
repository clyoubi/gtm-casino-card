# GTM Casino Card
This plugin provides a custom shortcode that displays detailed information about a casino in a predefined, visually styled card format. </br>
Each casino card is dynamically rendered with a shortcode based on a unique identifier, making it easy for content editors to insert specific casino profiles anywhere across the site.</br>
The plugin is designed for flexibility, seamless integration, and ease of use within the WordPress block editor or classic editor.

## 🚀 Installation

### Step-by-Step Instructions

#### 1. Log into WordPress Admin
Go to `https://yourdomain.com/wp-admin` and log in with your credentials. (if your website has a different admin panel slug to login, please contact your super administrator)

#### 2. Navigate to the Plugin Upload Page
From the left sidebar, go to:  
**Plugins > Add New**

#### 3. Upload the Plugin ZIP
- Click the **“Upload Plugin”** button at the top.
- Click **“Choose File”** and select the plugin ZIP file **gtm-casino-card.zip**.
- Click **“Install Now”**

#### 4. Activate the Plugin
Once the plugin has been successfully installed, click **“Activate Plugin”**

**The app will unistall itself if it detect a clash with another plugin using the same shortcode name id to avoid mismatches.**

---

## 🛠 Troubleshooting

- **"The link you followed has expired"**  
  → Increase `upload_max_filesize` and `post_max_size` in your `php.ini` or `.htaccess` file.

- **Installation Fails**  
  → Make sure your ZIP archive contains the plugin files inside a properly named root folder (not just raw `.php` files at the top level).

---

## ⚙ Configuration
The settings configuation can be found in the casino setting's page, the page is only available for administrators. Here is a detailled table of the core functionnalities.

<table class="table">
<thead>
    <tr>
        <th scope="col">Setting</th>
        <th scope="col">Description</th>
        <th scope="col">Default Value</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td>API Username</td>
        <td>The username used for authenticating requests to the casino data API.</td>
        <td>N/A</td>
    </tr>
    <tr>
        <td>API Password</td>
        <td>The password used for authenticating requests to the casino data API.</td>
        <td>N/A</td>
    </tr>
    <tr>
        <td>Currency for Bonus</td>
        <td>The currency symbol displayed for all monetary values on the casino card.</td>
        <td>€</td>
    </tr>
    <tr>
        <td>Enable Cache</td>
        <td>Activates transient caching to improve performance by reducing external API calls.</td>
        <td>no</td>
    </tr>
    <tr>
        <td>Cache Duration (hours)</td>
        <td>Defines how many hours the fetched data should remain cached before being refreshed.</td>
        <td>1</td>
    </tr>
    <tr>
        <td>Display All Casinos</td>
        <td>When enabled, displays all available casinos if no specific ID is passed in the shortcode.</td>
        <td>no</td>
    </tr>
    <tr>
        <td>Enable Dark Mode</td>
        <td>Displays the casino card using a dark theme, adapting to the user’s system preference.</td>
        <td>no</td>
    </tr>
    <tr>
        <td>Header Logo Display</td>
        <td>Shows the brand name next to the logo in the card header. This can be overridden in the shortcode.</td>
        <td>yes</td>
    </tr>
    <tr>
        <td>Header Background Color</td>
        <td>Default background color for the logo section in the card.</td>
        <td>#000000</td>
    </tr>
    <tr>
        <td>CTA Button Color</td>
        <td>Background color of the call-to-action button displayed in the card.</td>
        <td>#287e29</td>
    </tr>
</tbody>
</table>

---

## Usage
the plugin can be used by anyone accessing the admin panel, users with edit_posts capabilities can access to the documentation page and users with administrator capability can access and edit settings.

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
        <td><strong>id (required)</strong>
        </td>
        <td>id is the unique identifier for the casino. If not set, the shortcode will list and display all the Casinos</td>
        <td>N/A</td>
        <td><code>[casino_card id="3c3c3c3c-3c3c-3c3c-3c3c-3c3c3c3c3c3c"]</code></td>
    </tr>
    <tr>
        <td><strong>cta_color</strong></td>
        <td>cta_color defines the background color of the CTA button "Play now". When defined, it overides the general color configuration set in the settings</td>
        <td></td>
        <td><code>[casino_card cta_color="#000"]</code></td>
    </tr>
    <tr>
        <td><strong>header_color</strong></td>
        <td>header_color defines the background color of the header where the logo is located. When defined, it overides the general color configuration set in the settings</td>
        <td></td>
        <td><code>[casino_card header_color="#000"]</code></td>
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
        <td>no</td>
        <td><code>[casino_card auto_dark_mode="yes"]</code></td>
    </tr>
    <tr>
        <td><strong>display_brand_name</strong></td>
        <td>Defines if the header should display the brand name along side it's logo.</td>
        <td>no</td>
        <td><code>[casino_card display_brand_name="yes"]</code></td>
    </tr>
</tbody>
</table>

---

### Test connection
When the API credentials are set, you can test the connection to ensure they are correct.
Casino Card -> Settings -> Test Connection

### 🧠 Cache
To optimize performance and reduce redundant API requests, the plugin supports caching via WordPress Transients.

* **Enable Cache:** You can enable or disable the cache from the plugin settings (Enable Cache checkbox).
* **Cache Duration:** Set the duration (in hours) for which API responses should be cached. After expiration, fresh data will be fetched.
* **Manual Clear:** A button in the settings page allows you to manually clear the cache if needed.
* **Automated Clearing:** The plugin uses WordPress Cron to automatically clear cached data based on the duration you set.

#### Custom Currencies
You can define which currency symbol should be used when displaying monetary values.

* Available Options: EUR (€), USD ($) by default.
* Setting Location: Under General Settings, use the Currency For Bonus dropdown.
* Extendability: Developers can extend supported currencies by modifying the Casino::$currencies `public static $currencies = ['EUR' => '€', 'USD' => '$'];` variable in Casino class.


---

## ⚡️ Impact Analysis

### ✅ Performance
Remote API Fetching: When the `[casino_card]` shortcode is rendered without caching, it triggers a real-time API request to an external server. This:
* Adds latency depending on the response time of the third-party API.
* Can delay server-side rendering of the page.


#### Data Fetching
The plugin fetches casino data from a remote REST API via `wp_remote_get()`. Without caching, repeated shortcodes on the same or different pages could result in multiple API calls per request.

To mitigate this, transient caching is implemented:

* Each unique casino ID generates a separate cache key.
* Cached data is reused during the defined time window (casino_cache_delay in hours).
* Cache can be purged manually or via a WordPress cron job.
* logo images are load with async to not impact the TTFb

#### Resource Usage
Minimal impact on CPU and memory due to use of native PHP functions and WordPress APIs.
* Only necessary assets (style.css) are enqueued conditionally if the `[casino_card]` shortcode is detected in the content.
* Object-oriented PHP usage improves code modularity and clarity without performance penalty.

#### Recommendations
* Ensure API caching is enabled `(casino_general_enable_cache = yes)` in high-traffic environments.
* Avoid overusing shortcodes in high-density areas (e.g., listing dozens of casinos on a single page).

### ⚠️ Plugin Conflict
#### Shortcode Naming
* The plugin registers the  `[casino_card]` shortcode and verifies its availability before registration.
* If another plugin or theme has already registered the shortcode, this plugin:
* Automatically deactivates itself
* Displays an admin notice to prevent unexpected behavior

#### Settings Page & Menu Slugs
* Admin menu uses unique slugs (gtm-casino-card, casino_card_settings) to avoid collisions.
* Custom settings are namespaced using casino_ and gtm_ prefixes.

#### Recommendations
* Avoid installing other plugins that use the same  `[casino_card]` shortcode or access the same API endpoint namespace.
* Perform QA testing when new plugins are added, especially those involving custom shortcodes or external API calls.

### 🔒 Security
#### API Authentication
* Uses HTTP Basic Auth for communication with the external casino data API.
* Credentials (username, password) are stored securely using WordPress options API and not exposed in the frontend.

#### Data Sanitization & Validation
* All settings are escaped using `esc_attr()`, `esc_html()`, and validated with WordPress sanitizers.
* JSON responses are parsed and validated before display.
* Inputs from shortcodes are filtered via `shortcode_atts()` to prevent arbitrary parameter injection.

#### Transients & Cleanup
* All transient keys are prefixed and tracked to prevent persistent bloat.
* Admin button and scheduled cron allow cache cleanup, reducing attack surface through stale data exposure.

#### Access Control
* Only users with manage_options capability (typically Admins) can change settings or view sensitive config.
* Shortcode execution does not expose internal settings or secrets.

---

Here’s a polished and more comprehensive version of your Markdown documentation for shortcode registration in your WordPress plugin structure:

### 💻 Shortcode Registration Guide
This guide explains how to register a new shortcode in your custom WordPress plugin by following the established folder structure and class conventions.

#### 📂 Folder Structure
To register a new shortcode, create a subdirectory inside the shortcodes folder as follows:


```PHP
shortcodes/
└── ShortcodeNameSlug/                  # e.g., CasinoBanner
    ├── models/                         # Place related data models here (optional)
    ├── public/                         # Assets for frontend
    │   ├── css/
    |   |────style.css                  # frontend style to enqueue automatically
    │   ├── js/
    |    |────js.js                     # Javascript to enqueue automatically
    │   └── images/
    ├── templates/
    │   ├── admin.php                   # Admin page view for documentation or usage guide
    │   └── ShortcodeNameSlug.php      # Frontend rendering view you can access to data with $data returned from you overriden function (build_shortcode)
    └── ShortcodeNameSlug.php          # Class definition for the shortcode

```
#### 🧩 Class Definition
Each shortcode must define a class with the following naming convention:

```PHP
class GTM{ShortcodeNameSlug}ShortCode extends GTM_ShortCode {
    // Optional override methods from GTM_ShortCode
}
```
Example

```PHP
class GTMCasinoBannerShortCode extends GTM_ShortCode {
    public function build_shortcode(array $atts) {
        // Shortcode logic
    }

    public function register_settings() {
        // Optional admin settings
    }

    public function define() {
        // Optional metadata definition (title, description, default atts)
    }
}
```
#### 🔌 Interface Requirements
All shortcode classes must implement the IGTM_ShortCode interface:

```PHP
interface IGTM_ShortCode
{
    public function build_shortcode(array $atts);
    public function register_settings();
    public function define();
}
```

#### ⚙️ Automatic Discovery
Shortcodes placed in the correct folder and following the naming conventions are automatically discovered and registered by the plugin's base class (GTMCasinoCard or equivalent).

#### 🛠 Rendering Views
templates/ShortcodeNameSlug.php → Renders the frontend HTML for the shortcode.

templates/admin.php → Provides a reference/admin page for documentation or instructions within the WP dashboard.

#### 📝 Example Usage
To add a new CasinoBanner shortcode:

Create a folder: shortcodes/CasinoBanner

Add your assets and templates.

Create CasinoBanner.php in that folder with:

```PHP
class GTMCasinoBannerShortCode extends GTM_ShortCode {
    public function build_shortcode(array $atts) {
        // Define your shortcode logic here
        return $data = [
            status => true | false
            extras => []  // extras data to parse to the view
            data   => []  // array of object or object data to send to the view
        ]
    }

    public function register_settings() {
        // Optional: Register admin settings here
    }

    public function define() {
        $this->name = 'Casino Banner';
        $this->description = 'Displays a promotional casino banner.';
        $this->id = 'casino_banner';
    }
}
```

---

## 🚀 Deployment Documentation

This WordPress plugin uses GitHub Actions to automate the build and release process, including distribution to Telegram.

---

### 🔧 GitHub Actions CD/CI Pipeline

#### Overview

The GitHub Actions pipeline performs the following:

1. Validates PHP syntax.
2. Packages the plugin into a `.zip` file with proper folder structure.
3. Creates a GitHub release with the `.zip` as an asset.
4. Sends the release file to a specified Telegram channel. (You can adjust with notification system you want Slack, Teams, Mail...)
5. Automatically populate Updates to the plugin extension so it can be directly update from the admin dashboard on Wordpress
---

### 🔐 Required GitHub Secrets

Create these secrets in your repository under **Settings → Secrets → Actions**:

| Secret Name              | Description                                                                 |
|--------------------------|-----------------------------------------------------------------------------|
| `PUBLIC_RELEASE_TOKEN`   | GitHub Personal Access Token with `repo` permission (classic token).        |
| `TELEGRAM_BOT_TOKEN`     | Telegram bot token obtained via [@BotFather](https://t.me/BotFather).       |
| `TELEGRAM_CHAT_ID`       | Telegram channel or group chat ID (e.g., `@channelname` or `-100xxxxxxxxx`).|

---

### 🏷️ Tag-Based Release Trigger

The pipeline is triggered **when a tag is pushed** that matches the `v*.*.*` pattern.

#### Infos
* ChangeLog: You can provide a Changelog.md file listing the changes made in the current version
* version: **You have to update the plugin header version in the gtm-casino-card.php file else the "new update avaiblability" will not be propagated to all the websites.
```
 * Version: 1.1.5
``` 
* Stable version: if your update fix a critical bug, dont forget to update the stable version tag of the plyugin inside the README.txt
```
* Stable tag: v1.1.5
``` 

#### Example Tag Command

```bash
git tag v1.0.0
git push origin v1.0.0
```
