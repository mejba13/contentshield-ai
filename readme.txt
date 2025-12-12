=== ContentShield AI - Content Protection & Plagiarism Defense ===
Contributors: mejba
Tags: plagiarism, content protection, watermark, copyright, dmca
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI-powered content protection and plagiarism defense. Protect your content with invisible watermarks, fingerprinting, and plagiarism detection.

== Description ==

**ContentShield AI** helps content creators protect their work from theft and plagiarism through advanced technology:

= Free Features =

* **Invisible Watermarking** - Zero-width character fingerprints embedded in your content that survive copy-paste
* **Content Fingerprinting** - SimHash-based content identification for detecting stolen content
* **Manual URL Scanner** - Check any URL to see if it contains your content
* **Copy-Paste Detection** - JavaScript-based monitoring of copy attempts on your site
* **RSS Feed Protection** - Add attribution links to your RSS feed content
* **Basic Reports** - Track your protected content and scan history

= Pro Features =

Upgrade to Pro for advanced protection:

* Automated web monitoring (scheduled crawling)
* Real-time plagiarism alerts
* AI-powered content matching (semantic similarity)
* Google Search API integration
* Automated DMCA takedown generation
* One-click DMCA submission to Google/Bing
* Historical tracking & analytics dashboard
* Priority email support

= How It Works =

1. **Install & Activate** - Simply install the plugin and your content is automatically protected
2. **Fingerprinting** - When you publish content, ContentShield generates a unique fingerprint
3. **Watermarking** - Invisible zero-width characters are embedded in your content
4. **Scanning** - Manually check URLs or upgrade to Pro for automated monitoring
5. **Take Action** - When plagiarism is detected, take action with DMCA templates

= Privacy & Data =

ContentShield AI respects your privacy:

* All fingerprinting and watermarking happens locally on your server
* No content is sent to external servers in the free version
* Pro features communicate with our API using secure HTTPS connections
* We never store your actual content, only fingerprints and metadata

== Installation ==

1. Upload the `contentshield-ai` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to ContentShield in your admin menu to view the dashboard
4. Configure settings under ContentShield > Settings
5. Your content is now automatically protected!

= Manual Installation =

1. Download the plugin zip file
2. Go to Plugins > Add New in your WordPress admin
3. Click Upload Plugin and choose the zip file
4. Click Install Now, then Activate

== Frequently Asked Questions ==

= Does this plugin slow down my site? =

No. ContentShield AI is designed for performance. Fingerprinting happens once when content is published, and watermarking adds minimal overhead (zero-width characters are invisible and don't affect rendering).

= Will the watermarks be visible to my readers? =

No. Watermarks use zero-width Unicode characters that are completely invisible to readers but can be detected when content is copied.

= Can plagiarists remove the watermarks? =

While technically possible, removing zero-width characters requires technical knowledge. Most content scrapers don't bother, making watermarks effective for tracing stolen content.

= What happens when plagiarism is detected? =

You'll receive an alert in your dashboard. In the free version, you can use our DMCA templates to file takedown notices. Pro users get automated DMCA submission.

= Does the free version actually work? =

Yes! The free version includes fully functional watermarking, fingerprinting, and manual scanning. Pro adds automation and advanced features, but the core protection works great for free.

= How does the similarity detection work? =

We use SimHash, a locality-sensitive hashing algorithm that generates fingerprints which remain similar for similar content. This allows us to detect plagiarism even when text is slightly modified.

= Can I use this on multiple sites? =

The free version works on unlimited sites. Pro licenses are limited by plan (Starter: 1 site, Pro: 5 sites, Agency: 50 sites).

= Is my data safe? =

Yes. We follow WordPress security best practices. All data is stored locally in your WordPress database. Pro API communications use encrypted HTTPS connections.

== Screenshots ==

1. Dashboard - Overview of protected content and recent activity
2. Protected Content - View all fingerprinted posts and pages
3. Scanner - Check URLs for potential plagiarism
4. Alerts - Monitor and manage plagiarism alerts
5. Settings - Configure protection options
6. Pro Features - Upgrade for advanced protection

== Changelog ==

= 1.0.0 =
* Initial release
* Invisible watermarking with zero-width characters
* Content fingerprinting using SimHash algorithm
* Manual URL plagiarism scanner
* Copy-paste detection and logging
* RSS feed protection with attribution
* Admin dashboard with statistics
* Settings API for configuration
* REST API endpoints
* License system for Pro features
* WordPress coding standards compliant

== Upgrade Notice ==

= 1.0.0 =
Initial release of ContentShield AI. Start protecting your content today!

== External Services ==

This plugin connects to external services in the following cases:

= ContentShield API (Pro Only) =

When you activate a Pro license, the plugin communicates with our API at api.contentshield.ai for:

* License validation and activation
* Content registration for monitoring (fingerprints only, not actual content)
* Retrieving monitoring results and alerts
* DMCA template generation

Data sent: Site URL, license key hash, content fingerprints, post metadata (titles, URLs).
Data NOT sent: Actual post content, user data, visitor information.

Privacy Policy: https://contentshield.ai/privacy
Terms of Service: https://contentshield.ai/terms

= Manual URL Scanning =

When you scan a URL, the plugin fetches that URL's content directly from your server to compare against your fingerprints. No third-party services are used for this in the free version.

== Credits ==

* SimHash algorithm implementation
* Zero-width character encoding technique
* WordPress Plugin Boilerplate concepts
