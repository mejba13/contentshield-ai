# ContentShield AI

[![Built with Claude Code](https://img.shields.io/badge/Built%20with-Claude%20Code-blueviolet?logo=anthropic)](https://claude.ai/code)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue?logo=wordpress)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green)](https://www.gnu.org/licenses/gpl-2.0.html)

AI-powered content protection and plagiarism defense WordPress plugin. Protect your content with invisible watermarking, fingerprinting, and plagiarism detection.

<img width="2087" height="1419" alt="CleanShot 2025-12-12 at 6  11 29" src="https://github.com/user-attachments/assets/56a4c793-efba-4c4f-bc4c-9432c74e4ea6" />


## Features

### Free Features
- **Invisible Watermarking** - Embed zero-width Unicode characters into your content that are invisible to readers but traceable when copied
- **Content Fingerprinting** - SimHash algorithm creates unique fingerprints for your content that remain similar even when text is modified
- **Manual URL Scanning** - Check any URL for potential plagiarism of your content
- **RSS Feed Protection** - Add copyright notices and tracking to your RSS feeds
- **Copy Protection** - Optional right-click and text selection disabling

### Pro Features
- Automated monitoring and scheduled scans
- AI-powered plagiarism detection
- DMCA takedown templates and auto-submission
- Real-time alerts via email
- Priority support
- White-label reports (Agency plan)

## Installation

### From GitHub
1. Download the latest release
2. Upload to `/wp-content/plugins/contentshield-ai/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to ContentShield AI in the admin menu to configure

### From WordPress Admin
1. Go to Plugins > Add New
2. Upload the plugin ZIP file
3. Click "Install Now" and then "Activate"

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Configuration

1. Navigate to **ContentShield AI > Settings** in your WordPress admin
2. Configure watermarking options
3. Set up content types to protect
4. Enable/disable copy protection features
5. Add your Pro license key (optional)

## Usage

### Automatic Protection
Once activated, the plugin automatically:
- Generates fingerprints for new and updated posts
- Adds invisible watermarks to published content
- Protects RSS feeds with copyright notices

### Manual Scanning
1. Go to **ContentShield AI > Scanner**
2. Enter a URL you suspect may contain your content
3. Optionally select a specific post to compare against
4. Click "Scan URL" to check for plagiarism

### Viewing Protected Content
Go to **ContentShield AI > Protected Content** to see all fingerprinted content with their unique identifiers.

### Managing Alerts
The **Alerts** page shows potential plagiarism detections with severity ratings and resolution options.

## API Integration

ContentShield AI provides REST API endpoints for developers:

```
POST /wp-json/contentshield/v1/scan
POST /wp-json/contentshield/v1/fingerprint
GET  /wp-json/contentshield/v1/status
```

## Screenshots

The plugin features a modern, premium admin interface with:
- Clean dashboard with protection statistics
- Responsive design for all screen sizes
- Intuitive scanner interface
- Detailed alert management

## Changelog

### 1.0.0
- Initial release
- Content fingerprinting with SimHash
- Invisible watermarking
- URL plagiarism scanner
- RSS feed protection
- Alert management system
- Premium admin UI

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

- [Documentation](https://contentshield.ai/docs)
- [Support Forum](https://contentshield.ai/support)
- [GitHub Issues](https://github.com/mejba13/contentshield-ai.php/issues)

## License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

---

**Built with [Claude Code](https://claude.ai/code)** - AI-powered development by Anthropic
