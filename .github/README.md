# GitHub Workflows & Fixtures

This directory contains CI/CD workflows and test fixtures for the Autoloader Coordinator project.

## Workflows

### `wp-env-integration.yml`

Comprehensive integration testing workflow that:

- Tests version resolution across 5 scenarios
- Tests WordPress/PHP compatibility
- Posts PR comments with test results
- Validates plugin activation and registration

**Jobs:**
1. `version-resolution-test` - Tests all version conflict scenarios
2. `wp-compatibility-test` - Tests WordPress/PHP compatibility
3. `pr-comment-on-failure` - Posts warning comment on failure
4. `pr-comment-on-success` - Posts success comment on pass

## Fixtures

### `fixtures/scenarios/`

Test fixtures for version conflict scenarios. Each scenario contains:

- `plugin-a/composer.json` - Plugin A's package version
- `plugin-a/php/functions.php` - Plugin A's helper functions
- `plugin-b/composer.json` - Plugin B's package version
- `plugin-b/php/functions.php` - Plugin B's helper functions

**Scenarios:**
- `plugin-a-newer` - Plugin A has newer version
- `plugin-b-newer` - Plugin B has newer version
- `same-version` - Both have same version
- `major-version-diff` - Major version difference
- `patch-version-diff` - Patch version difference

### Adding New Scenarios

1. Create directory: `.github/fixtures/scenarios/[scenario-name]/`
2. Add `composer.json` and `php/functions.php` for each plugin
3. Add scenario to matrix in `wp-env-integration.yml`
4. Update documentation

## Workflow Details

### Version Resolution Test

Tests that the coordinator correctly resolves version conflicts:

- Installs fixtures for scenario
- Sets up wp-env
- Activates plugins
- Verifies coordinator loads
- Tests version resolution
- Validates homepage rendering

### WordPress Compatibility Test

Tests compatibility across WordPress/PHP versions:

- Tests on WordPress latest, 6.5, 6.4, 6.3
- Tests on PHP 8.2, 8.1, 7.4
- Validates coordinator works on all combinations

### PR Comments

Automatically posts comments on pull requests:

- **On Failure**: Shows which tests failed and possible causes
- **On Success**: Shows all passing tests and coverage

## Local Testing

To test workflows locally:

```bash
# Install act (GitHub Actions local runner)
brew install act  # macOS
# or download from https://github.com/nektos/act

# Run workflow
act -W .github/workflows/wp-env-integration.yml
```

Note: Local testing may require Docker and may not fully replicate CI environment.

## Troubleshooting

### Workflow Failures

1. Check workflow logs in GitHub Actions
2. Review error messages in failed steps
3. Test scenario locally with wp-env
4. Verify fixture files are correct
5. Check WordPress/PHP version compatibility

### Common Issues

**Helper functions missing:**
- Verify Composer autoload files are generated
- Check `functions.php` is in autoload_files.php
- Ensure fixtures are installed correctly

**Version resolution incorrect:**
- Verify fixture versions match expected
- Check plugin registration is working
- Clear coordinator cache

**wp-env not ready:**
- Increase wait timeout
- Check Docker is running
- Review wp-env logs

