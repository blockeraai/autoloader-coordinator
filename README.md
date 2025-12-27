# Autoloader Coordinator

A WordPress plugin that coordinates shared Composer package autoloading across multiple plugins, resolving version conflicts by automatically selecting the newest version.

## Overview

When multiple WordPress plugins ship the same Composer package (e.g., a shared utility library), conflicts can occur. The Autoloader Coordinator solves this by:

1. **Discovering** all versions of shared packages across registered plugins
2. **Comparing** versions using semantic versioning
3. **Selecting** the newest version automatically
4. **Loading** only the selected version's files
5. **Caching** results for performance

## Features

- ✅ Automatic version conflict resolution
- ✅ Semantic versioning comparison
- ✅ Transient caching for performance
- ✅ WordPress hook-based plugin registration
- ✅ Composer path repository support
- ✅ CI/CD integration with comprehensive testing

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher
- Composer 2.x

## Installation

### As a Composer Package

Add to your plugin's `composer.json`:

```json
{
  "require": {
    "blockera/autoloader-coordinator": "*"
  },
  "repositories": [
    {
      "type": "path",
      "url": "./packages/autoloader-coordinator",
      "options": {
        "symlink": true
      }
    }
  ]
}
```

Then run `composer install`.

### Plugin Integration

In your plugin's main file:

```php
// Register plugin with coordinator
add_filter('blockera/autoloader-coordinator/plugins/dependencies', function($dependencies) {
    $dependencies['your-plugin-slug'] = [
        'dir' => __DIR__,
        'priority' => 10,
        'default' => true, // Set to true if this plugin should win ties
    ];
    return $dependencies;
});

// Load coordinator
require_once __DIR__ . '/packages/autoloader-coordinator/loader.php';

// Register and bootstrap
\Blockera\SharedAutoload\Coordinator::getInstance()->registerPlugin();
\Blockera\SharedAutoload\Coordinator::getInstance()->bootstrap();
```

## Development Setup

### Prerequisites

- Node.js 20+
- PHP 8.2+
- Composer 2.x
- Docker (for wp-env)

### Local Development

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd autoloader-coordinator
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup wp-env**
   ```bash
   ./setup-wp-env.sh
   wp-env start
   ```

4. **Access WordPress**
   - URL: http://localhost:8888
   - Admin: http://localhost:8888/wp-admin
   - Username: `admin`
   - Password: `password`

### Running Tests

**Unit Tests:**
```bash
vendor/bin/phpunit tests/phpunit/Unit/
```

**Integration Tests:**
```bash
vendor/bin/phpunit tests/phpunit/Integration/
```

**wp-env Integration Tests:**
Tests run automatically via GitHub Actions. See `.github/workflows/wp-env-integration.yml` for details.

## Architecture

### Core Components

- **Coordinator Class** (`class-shared-autoload-coordinator.php`)
  - Singleton instance management
  - Plugin registration
  - Version comparison logic
  - Package manifest generation

- **Loader** (`loader.php`)
  - Entry point for plugins
  - Initializes coordinator
  - Handles autoload registration

### How It Works

1. **Plugin Registration**: Plugins register themselves via WordPress filter
2. **Package Discovery**: Coordinator scans registered plugins for Composer packages
3. **Version Collection**: Extracts version information from `composer.json` files
4. **Version Comparison**: Uses semantic versioning to determine newest version
5. **File Loading**: Loads only the winning version's autoload files
6. **Caching**: Stores results in WordPress transients for performance

### Version Resolution Logic

- **Different Versions**: Highest semantic version wins
- **Same Version**: Plugin marked as `default: true` wins
- **No Default**: First registered plugin wins

## Testing

### Test Scenarios

The project includes comprehensive integration tests covering:

1. **plugin-a-newer**: Plugin A has newer version (2.0.0 vs 1.0.0)
2. **plugin-b-newer**: Plugin B has newer version (1.0.0 vs 2.0.0)
3. **same-version**: Both plugins have same version (1.0.0)
4. **major-version-diff**: Major version difference (3.0.0 vs 1.0.0)
5. **patch-version-diff**: Patch version difference (1.0.0 vs 1.0.1)

### CI/CD

GitHub Actions workflows automatically test:
- All version resolution scenarios
- WordPress compatibility (6.3+)
- PHP compatibility (7.4, 8.1, 8.2)
- Plugin activation and registration
- Homepage rendering

See `.github/workflows/wp-env-integration.yml` for details.

## Project Structure

```
autoloader-coordinator/
├── packages/
│   └── autoloader-coordinator/     # Main coordinator package
│       ├── class-shared-autoload-coordinator.php
│       ├── loader.php
│       └── composer.json
├── plugin-a/                        # Test plugin A
├── plugin-b/                        # Test plugin B
├── tests/                           # PHPUnit tests
│   └── phpunit/
│       ├── Unit/
│       └── Integration/
├── .github/
│   ├── workflows/                   # CI/CD workflows
│   └── fixtures/                    # Test fixtures
│       └── scenarios/               # Version conflict scenarios
├── mu-plugins/                      # Must-use plugins
└── setup-wp-env.sh                 # Local setup script
```

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines.

## License

GPL-2.0-or-later

## Support

For issues and questions, please open an issue on GitHub.

