# Contributing to Autoloader Coordinator

Thank you for your interest in contributing! This document provides guidelines and instructions for contributing to the project.

## Development Setup

### Prerequisites

- PHP 7.4+ (8.2 recommended)
- Composer 2.x
- Node.js 20+
- Docker (for wp-env)
- Git

### Initial Setup

1. Fork and clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Run setup script:
   ```bash
   ./setup-wp-env.sh
   ```
4. Start WordPress environment:
   ```bash
   wp-env start
   ```

## Code Style

### PHP

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- Use PSR-4 autoloading
- Add type hints and return types where possible
- Use WordPress hooks for extensibility

### JavaScript/Shell

- Use consistent indentation (2 spaces)
- Follow shell best practices (use `set -e` for fail-fast)
- Add comments for complex logic

## Testing

### Before Submitting

1. **Run Unit Tests**
   ```bash
   vendor/bin/phpunit tests/phpunit/Unit/
   ```

2. **Run Integration Tests**
   ```bash
   vendor/bin/phpunit tests/phpunit/Integration/
   ```

3. **Test Locally with wp-env**
   ```bash
   wp-env start
   # Test manually or run CI workflow locally
   ```

4. **Check Code Style**
   ```bash
   composer check-cs  # If available
   ```

### Test Coverage

- Aim for high test coverage (>80%)
- Add tests for new features
- Update tests when fixing bugs
- Test edge cases and error conditions

## Pull Request Process

### Before Creating a PR

1. ✅ All tests pass locally
2. ✅ Code follows style guidelines
3. ✅ Documentation updated (if needed)
4. ✅ No merge conflicts
5. ✅ Commit messages are clear and descriptive

### PR Checklist

- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] Code reviewed (self-review at minimum)
- [ ] CI checks passing
- [ ] No breaking changes (or documented)

### Commit Messages

Use clear, descriptive commit messages:

```
feat: Add support for custom version comparators
fix: Resolve issue with transient cache invalidation
docs: Update README with new installation steps
test: Add integration test for patch version differences
```

## Adding New Features

### Version Resolution Scenarios

When adding new test scenarios:

1. Create fixture files in `.github/fixtures/scenarios/[scenario-name]/`
2. Add scenario to matrix in `.github/workflows/wp-env-integration.yml`
3. Document scenario in README.md
4. Add corresponding unit/integration tests

### Coordinator Enhancements

When modifying coordinator logic:

1. Update `class-shared-autoload-coordinator.php`
2. Add/update unit tests
3. Add/update integration tests
4. Update documentation
5. Test with all existing scenarios

## Debugging

### Common Issues

**Functions not loading:**
- Check Composer autoload files exist
- Verify `functions.php` is in `autoload_files.php`
- Clear coordinator cache: `delete_transient('blockera_pkgs_files')`

**Version resolution incorrect:**
- Verify `composer.json` versions match expected
- Check plugin registration filter is firing
- Clear all transients
- Check coordinator logs

**CI failures:**
- Review workflow logs
- Test locally with same scenario
- Check fixture files are correct
- Verify wp-env is accessible

### Debug Tools

- `mu-plugins/autoloader-coordinator-debug.php` - Frontend debugging
- `wp-content/debug.log` - PHP errors
- `wp eval` commands - Runtime inspection
- Coordinator cache inspection via transients

## Documentation

### When to Update Documentation

- Adding new features
- Changing behavior
- Fixing bugs that affect usage
- Adding new test scenarios
- Changing setup/installation process

### Documentation Files

- `README.md` - Main project documentation
- `CONTRIBUTING.md` - This file
- `ARCHITECTURE.md` - Technical architecture details
- Code comments - Inline documentation
- `.cursorrules` - Cursor IDE context

## Questions?

- Open an issue for bugs or feature requests
- Check existing issues before creating new ones
- Provide clear reproduction steps for bugs
- Include relevant code/logs when reporting issues

## Code Review

All PRs require review before merging. Reviewers will check:

- Code quality and style
- Test coverage
- Documentation completeness
- Backward compatibility
- Performance implications

Thank you for contributing! 🎉

