<?php
/**
 * PHPUnit bootstrap file for Autoloader Coordinator tests.
 *
 * @package Blockera\SharedAutoload\Tests
 */

// Define WordPress constants for testing.
if (! defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

if (! defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}

// Define global WordPress functions before loading Brain Monkey.
// These need to be real functions, not just stubs, because the Coordinator
// class calls them from within its namespace.
if (! function_exists('get_transient')) {
    function get_transient($transient) {
        global $__test_transients;
        return isset($__test_transients[$transient]) ? $__test_transients[$transient] : false;
    }
}

if (! function_exists('set_transient')) {
    function set_transient($transient, $value, $expiration = 0) {
        global $__test_transients;
        $__test_transients[$transient] = $value;
        return true;
    }
}

if (! function_exists('delete_transient')) {
    function delete_transient($transient) {
        global $__test_transients;
        unset($__test_transients[$transient]);
        return true;
    }
}

if (! function_exists('apply_filters')) {
    function apply_filters($hook, $value, ...$args) {
        // Brain Monkey will override this when needed.
        return $value;
    }
}

// Create a mock wpdb class for testing.
if (! class_exists('wpdb')) {
    class wpdb {
        public $options = 'wp_options';
        
        public function esc_like($text) {
            return addcslashes($text, '_%\\');
        }
        
        public function prepare($query, ...$args) {
            return vsprintf(str_replace('%s', "'%s'", $query), $args);
        }
        
        public function query($query) {
            return true;
        }
    }
}

// Initialize global $wpdb.
global $wpdb;
if (null === $wpdb) {
    $wpdb = new wpdb();
}

// Load Composer autoloader.
$autoloader = dirname(__DIR__, 2) . '/vendor/autoload.php';

if (! file_exists($autoloader)) {
    echo "Please run 'composer install' before running tests.\n";
    exit(1);
}

require_once $autoloader;

// Load the Coordinator class.
require_once dirname(__DIR__, 2) . '/packages/autoloader-coordinator/class-shared-autoload-coordinator.php';
