# CLAUDE.md

## Git Commit Instructions

When creating commits, always append the following attribution:

```
🤖 Generated with [Claude Code](https://claude.com/claude-code)
Co-Authored-By: Claude <noreply@anthropic.com>
```

## Project Overview

ContentShield AI is a WordPress plugin for content protection and plagiarism defense. It provides:

- Invisible watermarking using zero-width Unicode characters
- Content fingerprinting with SimHash algorithm
- Manual URL plagiarism scanning
- RSS feed protection
- Alert management system

## Development

- **PHP Version**: 7.4+
- **WordPress**: 6.0+
- **Main Plugin File**: `contentshield-ai.php`

## File Structure

```
contentshield-ai/
├── assets/
│   ├── css/admin.css      # Admin UI styles
│   └── js/admin.js        # Admin JavaScript
├── includes/
│   ├── admin/views/       # Admin page templates
│   ├── api/               # REST API endpoints
│   ├── core/              # Core functionality
│   └── public/            # Frontend functionality
├── contentshield-ai.php   # Main plugin file
└── uninstall.php          # Cleanup on uninstall
```
