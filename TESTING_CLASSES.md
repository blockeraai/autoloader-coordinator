# Testing Class Autoloading

This document explains how the `NameFormatter` class is used to verify PSR-4 class autoloading works correctly with the autoloader-coordinator.

## Overview

The `name-utils` package now includes both:
1. **Functions** (`functions.php`) - Tests file autoloading
2. **Classes** (`NameFormatter.php`) - Tests PSR-4 class autoloading

This ensures the coordinator handles both types of Composer autoloading correctly.

## Class Structure

### Location
- `plugin-a/packages/name-utils/php/NameFormatter.php`
- `plugin-b/packages/name-utils/php/NameFormatter.php`
- All fixture scenarios: `.github/fixtures/scenarios/*/plugin-*/php/NameFormatter.php`

### Namespace
```php
namespace Blockera\NameUtils;
```

### Class Methods

```php
class NameFormatter {
    public static function get_version(): string
    public static function get_loaded_from(): string
    public static function format( string $name ): string
    public static function get_metadata(): array
}
```

## Usage in Plugins

Both `plugin-a.php` and `plugin-b.php` now test class loading:

```php
// Test class loading (PSR-4 autoloading)
if ( class_exists( '\Blockera\NameUtils\NameFormatter' ) ) {
    $class_version = \Blockera\NameUtils\NameFormatter::get_version();
    $class_loaded_from = \Blockera\NameUtils\NameFormatter::get_loaded_from();
    $formatted = \Blockera\NameUtils\NameFormatter::format( 'Plugin A User' );
    
    // Display results in admin notices
}
```

## Testing in CI Workflow

### Current Test (Functions Only)

The workflow currently tests:
```php
$version = blockera_name_utils_get_version();
$loaded_from = blockera_name_utils_get_loaded_from();
```

### Recommended Addition (Class Testing)

Add this to the "Test version resolution" step in `.github/workflows/wp-env-integration.yml`:

```yaml
- name: Test class loading
  run: |
    set +e
    CLASS_RESULT=$(npx --yes @wordpress/env run cli wp eval "
      if (!class_exists('\\\\Blockera\\\\NameUtils\\\\NameFormatter')) {
        echo 'ERROR:CLASS_NOT_FOUND';
        exit(1);
      }
      \$version = \\Blockera\\NameUtils\\NameFormatter::get_version();
      \$loaded_from = \\Blockera\\NameUtils\\NameFormatter::get_loaded_from();
      echo \$version . '|' . \$loaded_from;
    " 2>&1 | grep -E '(version|plugin|ERROR:)' | head -1 | cut -d'|' -f1-2)
    
    if echo "$CLASS_RESULT" | grep -q "ERROR:"; then
      echo "=========================================="
      echo "ERROR: NameFormatter class not found!"
      echo "=========================================="
      exit 1
    fi
    
    CLASS_VERSION=$(echo "$CLASS_RESULT" | cut -d'|' -f1)
    CLASS_LOADED_FROM=$(echo "$CLASS_RESULT" | cut -d'|' -f2)
    
    if [ "$CLASS_VERSION" != "${{ matrix.expected_version }}" ]; then
      echo "ERROR: Class version mismatch. Expected: ${{ matrix.expected_version }}, Got: $CLASS_VERSION"
      exit 1
    fi
    
    if [ "$CLASS_LOADED_FROM" != "${{ matrix.expected_winner }}" ]; then
      echo "ERROR: Class loaded from wrong plugin. Expected: ${{ matrix.expected_winner }}, Got: $CLASS_LOADED_FROM"
      exit 1
    fi
    
    echo "✅ Class loading verified: v$CLASS_VERSION from $CLASS_LOADED_FROM"
    set -e
```

## Version-Specific Behavior

Each scenario has version-specific class implementations:

### plugin-a-newer
- Plugin A: v2.0.0 (uses `ucwords()` in format method)
- Plugin B: v1.0.0 (simple format)

### plugin-b-newer
- Plugin A: v1.0.0 (simple format)
- Plugin B: v2.0.0 (uses `ucwords()` in format method)

### same-version
- Both: v1.0.0 (plugin-a wins due to `default: true`)

### major-version-diff
- Plugin A: v3.0.0 (uses `ucwords()` in format method)
- Plugin B: v1.0.0 (simple format)

### patch-version-diff
- Plugin A: v1.0.0 (simple format)
- Plugin B: v1.0.1 (simple format, but newer patch)

## Verification Checklist

When testing, verify:

- [ ] Class exists: `class_exists('\Blockera\NameUtils\NameFormatter')`
- [ ] Version matches expected: `NameFormatter::get_version() === expected`
- [ ] Loaded from correct plugin: `NameFormatter::get_loaded_from() === expected`
- [ ] Class methods work: `NameFormatter::format()` returns correct string
- [ ] Metadata is correct: `NameFormatter::get_metadata()` matches version

## Debugging

If class loading fails:

1. **Check Composer autoload files:**
   ```bash
   wp-env run cli cat plugin-a/vendor/composer/autoload_psr4.php
   ```

2. **Verify PSR-4 mapping:**
   ```bash
   wp-env run cli wp eval "print_r(require 'plugin-a/vendor/composer/autoload_psr4.php');"
   ```

3. **Check coordinator manifest:**
   ```bash
   wp-env run cli wp eval "
     \$coordinator = \Blockera\SharedAutoload\Coordinator::getInstance();
     print_r(\$coordinator->getPackageManifest());
   "
   ```

4. **Test class directly:**
   ```bash
   wp-env run cli wp eval "
     require_once 'plugin-a/packages/name-utils/php/NameFormatter.php';
     echo \Blockera\NameUtils\NameFormatter::get_version();
   "
   ```

## Benefits

Adding class testing ensures:

1. **PSR-4 autoloading works** - Classes are loaded correctly
2. **Version resolution applies to classes** - Only winning version's class is loaded
3. **No class conflicts** - Multiple versions don't cause fatal errors
4. **Coordinator handles all autoload types** - Both files and classes work

