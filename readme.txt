=== Modular AI Assistant ===
Contributors: considadam
Tags: ai, content generation, editor help, snippets
Requires at least: 5.9
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.7
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add AI-powered modules to your WordPress site for content analysis, generation, and intelligent interactions.

== Description ==

Modular AI Assistant is a powerful WordPress plugin that brings AI capabilities directly into your website. Create custom AI-powered modules that can analyze content, answer questions, generate text, and provide intelligent interactions with your site visitors.

= Key Features =

* **AI Module Builder** - Create custom AI modules with configurable prompts and behaviors
* **Multiple AI Models** - Support for OpenAI and other AI providers
* **Shortcode Integration** - Easy embedding with `[modular-ai-assistant]` shortcode
* **Streaming Responses** - Real-time AI response streaming for better UX
* **Smart Caching** - Configurable response caching to reduce API costs
* **Content Adapters** - Automatically extract content from posts, pages, and custom post types
* **API Access** - REST API endpoints for external integrations
* **Editor Integration** - Analyze content directly from the WordPress editor
* **Customizable Appearance** - Configure colors to match your brand
* **Secure API Keys** - Built-in API key management for external access

= Use Cases =

* **Content Analysis** - Let AI analyze and suggest improvements to your content
* **Q&A Systems** - Create intelligent FAQ sections that understand context
* **Content Generation** - Generate text based on your existing content
* **Translation & Localization** - Smart content translation

= Developer Friendly =

* Modern PHP architecture with service providers
* Clean, extensible codebase
* Comprehensive hooks and filters
* REST API endpoints
* Custom content adapters
* Full i18n/l10n support

= Privacy & Security =

* Nonce verification for all forms
* Capability checks for admin access
* API key authentication for external requests
* Sanitized and escaped output
* GDPR compliant (no personal data stored by default)

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Modular AI Assistant"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the ZIP file and click "Install Now"
5. Activate the plugin

= Post-Installation Setup =

1. Navigate to Modular AI Assistant > Models in your admin panel
2. Create a new AI Model with your OpenAI API key
3. Create a new Module under Modular AI Assistant > Modules
4. Configure your module's prompts and settings
5. Use the shortcode `[modular-ai-assistant id="YOUR_MODULE_ID"]` to embed it on any page or post

= Requirements =

* PHP 7.4 or higher
* WordPress 5.9 or higher
* An OpenAI API key (or compatible AI service)

== Frequently Asked Questions ==

= Do I need an OpenAI API key? =

Yes, you need an API key from OpenAI or a compatible AI service provider. You can get one from https://platform.openai.com/api-keys

= How much does it cost? =

Modular AI Assistant itself is free. However, you'll need to pay for API usage from your chosen AI provider (OpenAI, etc.). The plugin includes caching features to help minimize API costs.

= Can I use streaming responses? =

Yes! The plugin supports both streaming and non-streaming responses. Streaming provides a better user experience by showing the AI response in real-time.

= Is it GDPR compliant? =

The plugin doesn't store user personal data by default. However, if you use it to process user input, ensure your privacy policy covers AI processing and inform users accordingly.

= Can I customize the appearance? =

Yes! Go to Modular AI Assistant > Settings > Appearance to customize the primary and secondary colors used in the frontend display.

= How do I disable the shortcode? =

Navigate to Modular AI Assistant > Settings > General and uncheck "Enable Shortcode". This will disable all shortcode functionality across your site.

= Does it support multiple languages? =

Yes, the plugin is fully translation-ready with support for WordPress i18n/l10n.

== Changelog ==

= 1.0.7 =
* Added a fix for a bug that occured when installing the npm packages in Github CI.

= 1.0.6 =
* Added fix for resetting postbox filter and contain paint. Some wordpress themes tries to make the postboxes better looking and add awful filters to them which causes position fixed elements to be trapped inside

= 1.0.5 =
* New name and improvements

= 1.0.4 =
* Added PHPCS comments and tweaks to satisfy the new Wordpress requirements

= 1.0.3 =
* Moved the helpers away from the composer autoload to a regular require_once

= 1.0.2 =
* Fixed automated build script on composer install/update for the plugin type

= 1.0.1 =
* Added support for Composer Packagist distribution with build scripts and dual autoloading

= 1.0.0 =
* Initial release
* AI module builder with custom prompts
* OpenAI integration with streaming support
* Shortcode embedding system
* REST API endpoints
* Smart caching system
* Content adapters for posts and pages
* Editor integration for content analysis
* API key management
* Customizable appearance settings
* Multi-language support

== Upgrade Notice ==

= 1.0.0 =
Initial release of Modular AI Assistant.

== Development ==

For development instructions and advanced usage, please visit the [plugin repository](https://github.com/Alingsas-Kommun/modular-ai-assistant).

== Additional Information ==

= Support =

For support, feature requests, or bug reports, please visit the plugin's support forum or GitHub repository.

= Credits =

* Developed by Adam Alexandersson
* Uses OpenAI API for AI capabilities
* Built with modern PHP architecture and Vite for asset bundling

= Privacy Policy =

Modular AI Assistant does not collect or store personal user data. When users interact with AI modules, their queries may be sent to the configured AI service provider (e.g., OpenAI). Please review your AI provider's privacy policy and ensure your site's privacy policy reflects this.

