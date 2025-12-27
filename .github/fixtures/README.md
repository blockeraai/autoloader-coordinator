# Test Fixtures for Autoloader Coordinator

This directory contains **scenario-based fixtures** used by the CI workflow to test version conflict resolution across multiple scenarios.

## Structure

```
fixtures/
├── README.md
└── scenarios/
    ├── plugin-a-newer/       # Plugin A has v2.0.0, Plugin B has v1.0.0
    │   ├── plugin-a/
    │   │   ├── composer.json
    │   │   └── php/
    │   │       └── functions.php
    │   └── plugin-b/
    │       ├── composer.json
    │       └── php/
    │           └── functions.php
    │
    ├── plugin-b-newer/       # Plugin A has v1.0.0, Plugin B has v2.0.0
    │   └── ... (same structure)
    │
    ├── same-version/         # Both have v1.0.0 (tests default/priority)
    │   └── ...
    │
    ├── major-version-diff/   # Plugin A has v3.0.0, Plugin B has v1.0.0
    │   └── ...
    │
    └── patch-version-diff/   # Plugin A has v1.0.0, Plugin B has v1.0.1
        └── ...
```

## Scenarios

| Scenario | Plugin A Version | Plugin B Version | Expected Winner | Tests |
|----------|-----------------|------------------|-----------------|-------|
| `plugin-a-newer` | 2.0.0 | 1.0.0 | plugin-a | Minor version comparison |
| `plugin-b-newer` | 1.0.0 | 2.0.0 | plugin-b | Minor version comparison |
| `same-version` | 1.0.0 | 1.0.0 | plugin-a | Default/priority fallback |
| `major-version-diff` | 3.0.0 | 1.0.0 | plugin-a | Major version comparison |
| `patch-version-diff` | 1.0.0 | 1.0.1 | plugin-b | Patch version comparison |

## How Scenarios Work

The CI workflow uses GitHub Actions matrix strategy to run each scenario:

1. **Matrix Definition**: Each scenario is defined in the workflow matrix
2. **Fixture Installation**: For each scenario, the workflow copies fixtures over plugin files:
   ```bash
   # Override plugin-a files
   cp .github/fixtures/scenarios/$SCENARIO/plugin-a/composer.json plugin-a/packages/name-utils/composer.json
   cp .github/fixtures/scenarios/$SCENARIO/plugin-a/php/functions.php plugin-a/packages/name-utils/php/functions.php
   
   # Override plugin-b files
   cp .github/fixtures/scenarios/$SCENARIO/plugin-b/composer.json plugin-b/packages/name-utils/composer.json
   cp .github/fixtures/scenarios/$SCENARIO/plugin-b/php/functions.php plugin-b/packages/name-utils/php/functions.php
   ```

3. **Verification**: The workflow verifies the correct version is loaded

## Helper Functions

Each fixture includes these functions:

| Function | Description |
|----------|-------------|
| `blockera_name_utils_get_version()` | Returns the version string |
| `blockera_name_utils_get_loaded_from()` | Returns which plugin loaded this package |
| `blockera_name_utils_get_metadata()` | Returns full package metadata including scenario |

## Adding New Scenarios

1. Create a new directory under `scenarios/`:
   ```
   scenarios/your-scenario-name/
   ├── plugin-a/
   │   ├── composer.json
   │   └── php/
   │       └── functions.php
   └── plugin-b/
       ├── composer.json
       └── php/
           └── functions.php
   ```

2. Add the scenario to the workflow matrix in `.github/workflows/wp-env-integration.yml`

3. Ensure both `functions.php` files return correct version info via the helper functions
