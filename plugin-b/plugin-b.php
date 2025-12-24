<?php
/**
 * Plugin Name: Autoloader Coordinator - Plugin B
 * Description: Plugin B for testing the autoloader coordinator.
 * Version: 1.0.0
 * Author: https://blockera.ai
 * Author URI: https://example.com/plugin-b
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: plugin-b
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * @package Plugin_B
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register into shared autoload coordinator.
add_filter('blockera/autoloader-coordinator/plugins/dependencies', function($dependencies) {
    $dependencies['plugin-b'] = [
        'dir' => __DIR__,
        'priority' => 10,
        'default' => false,
    ];
    return $dependencies;
});

// Register into shared autoload coordinator.
// This replaces vendor/autoload.php by loading directly from Composer-generated static files.
require_once __DIR__ . '/packages/autoloader-coordinator/loader.php';

// Register into shared autoload coordinator and bootstrap autoloading.
\Blockera\SharedAutoload\Coordinator::getInstance()->registerPlugin();
\Blockera\SharedAutoload\Coordinator::getInstance()->bootstrap();

// Invalidate package manifest cache on plugin activation, deactivation, and upgrade.
add_action('activated_plugin', [ \Blockera\SharedAutoload\Coordinator::getInstance(), 'invalidatePackageManifest' ]);
add_action('deactivated_plugin', [ \Blockera\SharedAutoload\Coordinator::getInstance(), 'invalidatePackageManifest' ]);
add_action('upgrader_process_complete', [ \Blockera\SharedAutoload\Coordinator::getInstance(), 'invalidatePackageManifest' ]);

echo plugin_b_print_name('Guest User');

