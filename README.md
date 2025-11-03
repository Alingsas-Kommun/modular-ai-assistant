# Modular AI

A WordPress plugin that integrates AI capabilities into your WordPress website. Build AI-powered modules that can analyze content, generate responses, and enhance user interactions.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Development](#development)
  - [Asset Building](#asset-building)
  - [Translations](#translations)
- [Architecture Overview](#architecture-overview)
- [Core Concepts](#core-concepts)
  - [Application & Service Providers](#application--service-providers)
  - [Entities](#entities)
  - [Content Extraction](#content-extraction)
- [Extending the Plugin](#extending-the-plugin)
  - [Custom HTML Content Filter](#custom-html-content-filter)
  - [Creating Content Adapters](#creating-content-adapters)
  - [Adding Service Providers](#adding-service-providers)
- [API Endpoints](#api-endpoints)
- [File Structure](#file-structure)

## Requirements

- PHP 7.4 or higher
- WordPress 5.9 or higher
- Composer
- Node.js & npm (for development)

## Installation

1. Clone or download the plugin to your WordPress plugins directory
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install Node dependencies (for development):
   ```bash
   npm install
   ```
4. Build assets:
   ```bash
   npm run build
   ```
5. Activate the plugin through the WordPress admin panel

## Development

### Asset Building

The plugin uses Vite for asset bundling. Assets are located in `resources/assets/` and are compiled to the `dist/` directory.

#### Available Scripts

**Development Mode**
```bash
npm run dev
```
Starts Vite in development mode with hot module replacement. This watches for changes and automatically rebuilds assets.

**Production Build**
```bash
npm run build
```
Builds optimized production assets with minification and hashing. The build includes:
- Admin JavaScript (`resources/assets/js/admin.js` → `dist/js/admin.[hash].js`)
- Frontend JavaScript (`resources/assets/js/frontend.js` → `dist/js/frontend.[hash].js`)
- Admin SCSS (`resources/assets/scss/admin.scss` → `dist/css/admin.[hash].css`)
- Frontend SCSS (`resources/assets/scss/frontend.scss` → `dist/css/frontend.[hash].css`)

**Preview Production Build**
```bash
npm run preview
```
Locally previews the production build.

#### Asset Loading

Assets are automatically enqueued using the ViteManifest utility class, which reads the generated manifest file to get the correct hashed filenames.

### Translations

The plugin uses WordPress i18n for translations. Translation files are located in `resources/languages/`.

#### Translation Scripts

**Generate/Update POT Template**
```bash
npm run translate:pot
```
Scans the `app/` and `resources/` directories for translatable strings and generates a `.pot` template file at `resources/languages/modular-ai.pot`.

**Update PO Files**
```bash
npm run translate:update
```
Updates all existing `.po` translation files with new strings from the `.pot` template.

**Complete Translation Workflow**
```bash
npm run translate
```
Combines both POT generation and PO file updates. Use this when you've added new translatable strings.

**Compile Translations**
```bash
npm run translate:compile
```
Compiles translation files for use:
- Generates `.mo` files from `.po` files
- Creates JSON translation files for JavaScript

**Generate MO Files**
```bash
npm run translate:mo
```
Compiles `.po` files to binary `.mo` files for PHP usage.

**Generate JSON Files**
```bash
npm run translate:js
```
Generates JSON translation files for JavaScript/frontend usage and runs a post-processing fix script.

#### Translation Workflow Example

1. Add translatable strings in your code:
   ```php
   __('My translatable text', 'modular-ai')
   ```
2. Generate/update the POT file:
   ```bash
   npm run translate:pot
   ```
3. Update existing translations:
   ```bash
   npm run translate:update
   ```
4. Edit `.po` files with your translations
5. Compile translations:
   ```bash
   npm run translate:compile
   ```

## Architecture Overview

ModularAI follows a modular, service provider-based architecture inspired by modern PHP frameworks.

### Key Components

- **Application**: Bootstrap class that registers and boots service providers
- **Service Providers**: Classes that register and configure services
- **Container**: Simple dependency injection container for managing class instances
- **Entities**: Custom post types with repositories (Modules, Models, API Keys)
- **Content System**: Extracts and processes content using adapters
- **HTTP Clients**: Abstracted API communication layer
- **API Endpoints**: REST API endpoints for frontend communication

## Core Concepts

### Application & Service Providers

The plugin boots through the `Application` class in `app/Application.php`:

```php
Application::configure()
    ->withProviders([
        PluginServiceProvider::class,
        AdminServiceProvider::class,
        FrontendServiceProvider::class,
        ApiServiceProvider::class,
    ])
    ->boot();
```

Service providers have two lifecycle methods:
- `register()`: Register services in the container
- `boot()`: Initialize services after all providers are registered

#### Creating a Service Provider

```php
namespace ModularAI\Providers;

use ModularAI\Abstracts\ServiceProvider;
use ModularAI\Utilities\Container;

class MyServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        // Register singletons or bindings
        $container->singleton(MyService::class);
    }

    public function boot(Container $container): void
    {
        // Boot services, add hooks, etc.
        $container->make(MyService::class);
    }
}
```

### Entities

Entities are custom post types with associated repositories and meta boxes. The plugin includes three entity types:

1. **Modules** (`mai_module`): AI interaction modules that users can embed
2. **Models** (`mai_model`): AI model configurations (GPT-4, etc.)
3. **API Keys** (`mai_api_key`): Encrypted API credentials

Each entity consists of:
- Post type class extending `ModularAI\Abstracts\PostType`
- Repository class for data access
- MetaBox classes for admin UI extending `ModularAI\Abstracts\MetaBox`

#### Creating a New Entity

1. Create a post type class:
```php
namespace ModularAI\Entities\MyEntity;

use ModularAI\Abstracts\PostType;

class MyEntity extends PostType
{
    protected static $post_type_slug = 'mai_my_entity';
    protected static $menu_icon = 'dashicons-admin-generic';

    protected function getLabels()
    {
        return [
            'name' => 'My Entities',
            'singular_name' => 'My Entity',
            // ... other labels
        ];
    }

    protected function getArgs()
    {
        return [
            'public' => true,
            'show_in_menu' => 'modular-ai',
            // ... other args
        ];
    }
}
```

2. Create a repository:
```php
namespace ModularAI\Entities\MyEntity;

class Repository
{
    public function __construct()
    {
        new MyEntity();
    }

    public function find($id): ?array
    {
        // Repository methods
    }
}
```

3. Register in a service provider:
```php
$container->singleton(MyEntityRepository::class);
```

### Content Extraction

The `ContentExtractor` class (`app/Content/ContentExtractor.php`) extracts and processes content from WordPress posts for AI processing.

**Key Features:**
- Extracts post content, title, and excerpts
- Converts HTML to clean Markdown for AI consumption
- Supports content adapters for third-party plugins
- Provides filters for customization

**Content Flow:**
1. Extract HTML content from post
2. Apply content filters
3. Process through registered adapters
4. Clean HTML (remove scripts, empty tags, etc.)
5. Convert to Markdown
6. Return to AI module

## Extending the Plugin

### Custom HTML Content Filter

The `modular_ai_custom_html_content` filter allows you to inject custom HTML content before it's converted to Markdown and sent to the AI.

**Filter Location:** `app/Content/ContentExtractor.php:73`

#### Filter Parameters

```php
apply_filters('modular_ai_custom_html_content', string $content, int $post_id, string $post_type): string
```

- `$content` (string): The current HTML content
- `$post_id` (int): The post ID being processed
- `$post_type` (string): The post type slug

#### Example: Adding Custom Meta Fields

```php
add_filter('modular_ai_custom_html_content', function($content, $post_id, $post_type) {
    // Only add for 'product' post type
    if ($post_type !== 'product') {
        return $content;
    }
    
    // Get custom meta fields
    $price = get_post_meta($post_id, 'product_price', true);
    $sku = get_post_meta($post_id, 'product_sku', true);
    
    // Build custom HTML to inject
    $custom_html = '<div class="product-info">';
    $custom_html .= '<p><strong>Price:</strong> ' . esc_html($price) . '</p>';
    $custom_html .= '<p><strong>SKU:</strong> ' . esc_html($sku) . '</p>';
    $custom_html .= '</div>';
    
    // Append to content
    return $content . $custom_html;
}, 10, 3);
```

#### Example: Adding ACF Fields

```php
add_filter('modular_ai_custom_html_content', function($content, $post_id, $post_type) {
    // Get ACF fields
    $specifications = get_field('specifications', $post_id);
    $features = get_field('features', $post_id);
    
    if ($specifications) {
        $content .= '<div class="specifications">';
        $content .= '<h3>Specifications</h3>';
        $content .= wp_kses_post($specifications);
        $content .= '</div>';
    }
    
    if ($features && is_array($features)) {
        $content .= '<div class="features">';
        $content .= '<h3>Features</h3>';
        $content .= '<ul>';
        foreach ($features as $feature) {
            $content .= '<li>' . esc_html($feature['name']) . '</li>';
        }
        $content .= '</ul>';
        $content .= '</div>';
    }
    
    return $content;
}, 10, 3);
```

#### Example: Conditional Content Based on User Role

```php
add_filter('modular_ai_custom_html_content', function($content, $post_id, $post_type) {
    // Add admin-only information if user is logged in and has appropriate role
    if (is_user_logged_in() && current_user_can('administrator')) {
        $edit_link = get_edit_post_link($post_id);
        $author = get_the_author_meta('display_name', get_post_field('post_author', $post_id));
        
        $content .= '<div class="admin-info">';
        $content .= '<p><strong>Author:</strong> ' . esc_html($author) . '</p>';
        $content .= '<p><strong>Edit Link:</strong> <a href="' . esc_url($edit_link) . '">Edit Post</a></p>';
        $content .= '</div>';
    }
    
    return $content;
}, 10, 3);
```

**Note:** The HTML you inject will be processed through `HtmlProcessor::cleanHtmlForMarkdown()` and then converted to Markdown before being sent to the AI. Structure your HTML appropriately for good Markdown conversion.

### Creating Content Adapters

Content adapters allow integration with third-party plugins by injecting their content into the extraction process. Adapters implement the `AdapterInterface`.

**Example Adapter Structure:**

```php
namespace ModularAI\Content\Adapters;

use ModularAI\Content\Interfaces\AdapterInterface;

class MyPluginAdapter implements AdapterInterface
{
    /**
     * Check if the third-party plugin is installed and active
     */
    public static function installed(): bool
    {
        return function_exists('my_plugin_function') || class_exists('MyPlugin\Class');
    }

    /**
     * Inject content from the third-party plugin
     */
    public function inject(string $content, int $post_id): string
    {
        // Get content from your plugin
        $plugin_data = get_my_plugin_data($post_id);
        
        if (!$plugin_data) {
            return $content;
        }
        
        // Build HTML to inject
        $injected_html = '<div class="my-plugin-content">';
        $injected_html .= wp_kses_post($plugin_data);
        $injected_html .= '</div>';
        
        // Append or insert at specific position
        return $content . $injected_html;
    }
}
```

**Register the Adapter:**

Add your adapter class to `app/Content/Adapters.php`:

```php
public static function getAdapterClasses(): array
{
    return [
        Modularity::class,
        MyPluginAdapter::class, // Add your adapter
    ];
}
```

The adapter will automatically be loaded if the third-party plugin is installed.

### Adding Service Providers

Service providers organize related functionality and dependencies.

**Creating a Service Provider:**

```php
namespace ModularAI\Providers;

use ModularAI\Abstracts\ServiceProvider;
use ModularAI\Utilities\Container;

class MyFeatureServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        // Register dependencies
        $container->singleton(MyFeatureService::class);
    }

    public function boot(Container $container): void
    {
        // Initialize services, add WordPress hooks
        $service = $container->make(MyFeatureService::class);
        
        add_action('init', function() use ($service) {
            $service->initialize();
        });
        
        add_filter('my_filter', function($value) use ($service) {
            return $service->processFilter($value);
        });
    }
}
```

**Register in Application:**

Edit `modular-ai.php`:

```php
Application::configure()
    ->withProviders([
        PluginServiceProvider::class,
        AdminServiceProvider::class,
        FrontendServiceProvider::class,
        ApiServiceProvider::class,
        MyFeatureServiceProvider::class, // Add your provider
    ])
    ->boot();
```

## API Endpoints

The plugin registers REST API endpoints under the `/modular-ai/v1/` namespace.

### Available Endpoints

- `GET /modular-ai/v1/modules` - List all available modules
- `GET /modular-ai/v1/models` - List all AI models
- `POST /modular-ai/v1/module/run` - Run a module with AI
- `POST /modular-ai/v1/model/test` - Test an AI model configuration
- `GET /modular-ai/v1/module/template` - Get module template HTML

### Creating Custom Endpoints

1. Create an endpoint class:

```php
namespace ModularAI\Api\Endpoints;

use ModularAI\Api\Abstracts\Endpoint;

class MyEndpoint extends Endpoint
{
    protected string $route = '/my-endpoint';
    protected string $method = 'POST';

    public function callback(\WP_REST_Request $request)
    {
        // Your endpoint logic
        return rest_ensure_response([
            'success' => true,
            'data' => 'your data',
        ]);
    }

    public function permission_callback(\WP_REST_Request $request): bool
    {
        // Permission check
        return current_user_can('read');
    }
}
```

2. Register in `ApiServiceProvider`:

```php
public function boot(Container $container): void
{
    // Existing endpoints...
    new MyEndpoint();
}
```

## File Structure

```
modular-ai/
├── app/                          # PHP application code
│   ├── Abstracts/                # Abstract base classes
│   │   ├── MetaBox.php
│   │   ├── PostType.php
│   │   └── ServiceProvider.php
│   ├── Admin/                    # Admin-specific functionality
│   ├── Api/                      # REST API endpoints
│   │   ├── Abstracts/
│   │   ├── Endpoints/
│   │   └── Traits/
│   ├── Assets/                   # Asset enqueuing
│   ├── Content/                  # Content extraction system
│   │   ├── Adapters/             # Third-party plugin adapters
│   │   ├── Interfaces/
│   │   └── Utilities/            # HTML and Markdown processing
│   ├── Entities/                 # Custom post types
│   │   ├── ApiKeys/
│   │   ├── Models/
│   │   └── Modules/
│   ├── Http/                     # HTTP client abstraction
│   │   └── Clients/              # AI provider clients (OpenAI, etc.)
│   ├── Providers/                # Service providers
│   ├── Services/                 # Business logic services
│   ├── Shortcodes/               # WordPress shortcodes
│   └── Utilities/                # Helper utilities
├── config/                       # Configuration files
├── dist/                         # Compiled assets (generated)
│   ├── css/
│   └── js/
├── resources/                    # Source files
│   ├── assets/
│   │   ├── js/                   # JavaScript source
│   │   └── scss/                 # SCSS source
│   ├── languages/                # Translation files
│   └── views/                    # PHP templates
├── vendor/                       # Composer dependencies
├── modular-ai.php             # Plugin entry point
├── composer.json                 # PHP dependencies
├── package.json                  # Node dependencies
├── vite.config.js               # Vite configuration
└── README.md                     # This file
```

## Usage

### Creating a Module

1. Navigate to **ModularAI > Modules** in WordPress admin
2. Click **Add New**
3. Configure the module:
   - **System Prompt**: Instructions for the AI
   - **User Prompt Type**: Choose what content to send (page content, title, excerpt, or custom)
   - **Model**: Select an AI model
   - **Output Format**: Choose Markdown or plain text
4. Publish the module

### Embedding a Module

Use the shortcode with your module ID:

```
[modular-ai module_id="123"]
```

Or programmatically:

```php
echo do_shortcode('[modular-ai module_id="123"]');
```

### API Key Configuration

1. Navigate to **ModularAI > API Keys**
2. Keys are encrypted before storage for security

---

## Contributing

When contributing, please:
1. Follow WordPress coding standards
2. Write clear commit messages
3. Test your changes thoroughly
4. Update documentation as needed
5. Add translations for new strings

## License

GPL v3 or later - see `license.txt`

## Author

Adam Alexandersson

