<?php
/**
 * Plugin Name: Autoloader Coordinator - Plugin A
 * Description: Plugin A for testing the autoloader coordinator.
 * Version: 1.0.0
 * Author: https://blockera.ai
 * Author URI: https://example.com/plugin-a
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: plugin-a
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * @package Plugin_A
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register into shared autoload coordinator.
add_filter('blockera/autoloader-coordinator/plugins/dependencies', function($dependencies) {
    $dependencies['plugin-a'] = [
        'dir' => __DIR__,
        'priority' => 10,
        'default' => true,
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

// Use name-utils package functions and classes (loaded via autoloader-coordinator)
add_action('admin_notices', function() {
    if ( ! function_exists( 'blockera_name_utils_get_version' ) || ! function_exists( 'blockera_name_utils_get_loaded_from' ) ) {
        return; // name-utils package not loaded yet
    }
    
    $version = blockera_name_utils_get_version();
    $loaded_from = blockera_name_utils_get_loaded_from();
    
    // Display version info in admin notices
    printf(
        '<div class="notice notice-info is-dismissible">
            <p><strong>Plugin A:</strong> name-utils package v%s loaded from %s</p>
        </div>',
        esc_html( $version ),
        esc_html( $loaded_from )
    );
    
    // Test class loading (PSR-4 autoloading)
    if ( class_exists( '\Blockera\NameUtils\NameFormatter' ) ) {
        $class_version = \Blockera\NameUtils\NameFormatter::get_version();
        $class_loaded_from = \Blockera\NameUtils\NameFormatter::get_loaded_from();
        $formatted = \Blockera\NameUtils\NameFormatter::format( 'Plugin A User' );
        
        printf(
            '<div class="notice notice-success is-dismissible">
                <p><strong>Plugin A:</strong> Class loaded - v%s from %s<br>
                <code>NameFormatter::format()</code> result: "%s"</p>
            </div>',
            esc_html( $class_version ),
            esc_html( $class_loaded_from ),
            esc_html( $formatted )
        );
    } else {
        printf(
            '<div class="notice notice-warning is-dismissible">
                <p><strong>Plugin A:</strong> <code>NameFormatter</code> class not found!</p>
            </div>'
        );
    }
    
    // Demonstrate v2.0.0+ exclusive feature if available
    if ( function_exists( 'blockera_format_name' ) ) {
        $formatted = blockera_format_name( 'plugin a user' );
        printf(
            '<div class="notice notice-success is-dismissible">
                <p><strong>Plugin A:</strong> Using v2.0.0+ feature: <code>blockera_format_name()</code> - "%s"</p>
            </div>',
            esc_html( $formatted )
        );
    }
});

// Display on frontend footer for testing
add_action('wp_footer', function() {
    if ( ! function_exists( 'blockera_name_utils_get_version' ) || ! function_exists( 'blockera_name_utils_get_loaded_from' ) ) {
        return;
    }
    
    $version = blockera_name_utils_get_version();
    $loaded_from = blockera_name_utils_get_loaded_from();
    
    // Only show on frontend for testing purposes
    if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
        echo sprintf(
            '<!-- Plugin A: name-utils v%s from %s -->',
            esc_html( $version ),
            esc_html( $loaded_from )
        );
    }
}, 999 );

// Legacy function for backward compatibility
if ( ! function_exists( 'plugin_a_print_name' ) ) {
    function plugin_a_print_name( string $name ): void {
        if ( function_exists( 'blockera_name_utils_get_version' ) ) {
            $version = blockera_name_utils_get_version();
            $loaded_from = blockera_name_utils_get_loaded_from();
            echo sprintf(
                'Hello, %s! (name-utils v%s from %s)',
                esc_html( $name ),
                esc_html( $version ),
                esc_html( $loaded_from )
            );
        } else {
            echo 'Hello, ' . esc_html( $name ) . '!';
        }
    }
}
