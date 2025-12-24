<?php

if (! function_exists('plugin_b_print_name')) {
    function plugin_b_print_name(string $name): void {
        echo 'Hello, ' . esc_html($name) . '!';
    }
}

