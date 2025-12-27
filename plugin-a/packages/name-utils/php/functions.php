<?php

if (! function_exists('plugin_a_print_name')) {
    function plugin_a_print_name(string $name): void {
        echo 'Hello, ' . esc_html($name) . '!';
    }
}
