# Quick Start Guide

## For Developers

### Setup (5 minutes)

```bash
# 1. Install dependencies
composer install && npm install

# 2. Setup wp-env
./setup-wp-env.sh

# 3. Start WordPress
wp-env start

# 4. Access WordPress
# URL: http://localhost:8888
# Admin: http://localhost:8888/wp-admin
# Username: admin / Password: password
```

### Common Tasks

**Run Tests:**
```bash
vendor/bin/phpunit
```

**Check WordPress:**
```bash
wp-env run cli wp plugin list
wp-env run cli wp eval "echo 'Hello';"
```

**View Logs:**
```bash
wp-env logs
```

**Stop Environment:**
```bash
wp-env stop
```

## For Contributors

### Before Making Changes

1. Read [ARCHITECTURE.md](ARCHITECTURE.md) to understand the system
2. Check [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines
3. Review existing tests to understand patterns

### Making Changes

1. Create feature branch
2. Make changes
3. Add/update tests
4. Run tests locally
5. Update documentation
6. Create PR

### Testing Checklist

- [ ] Unit tests pass
- [ ] Integration tests pass
- [ ] wp-env tests pass (or CI passes)
- [ ] No PHP errors/warnings
- [ ] Code follows style guidelines

## For Users

### Installation

Add to your plugin's `composer.json`:

```json
{
  "require": {
    "blockera/autoloader-coordinator": "*"
  },
  "repositories": [
    {
      "type": "path",
      "url": "./packages/autoloader-coordinator"
    }
  ]
}
```

### Integration

```php
// Register plugin
add_filter('blockera/autoloader-coordinator/plugins/dependencies', function($deps) {
    $deps['your-plugin'] = ['dir' => __DIR__, 'default' => true];
    return $deps;
});

// Load coordinator
require_once __DIR__ . '/packages/autoloader-coordinator/loader.php';
\Blockera\SharedAutoload\Coordinator::getInstance()->registerPlugin();
\Blockera\SharedAutoload\Coordinator::getInstance()->bootstrap();
```

## Troubleshooting

**Functions not available?**
- Check Composer autoload files exist
- Verify package is registered
- Clear coordinator cache

**Wrong version loaded?**
- Check `composer.json` versions
- Verify plugin registration
- Clear transients: `delete_transient('blockera_pkgs_files')`

**CI failing?**
- Check workflow logs
- Verify fixture files
- Test locally first

## Resources

- [Full Documentation](README.md)
- [Architecture Details](ARCHITECTURE.md)
- [Contributing Guide](CONTRIBUTING.md)
- [GitHub Workflows](.github/README.md)

