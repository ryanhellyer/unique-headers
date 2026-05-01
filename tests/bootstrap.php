<?php

/**
 * WordPress function stubs for unit tests.
 *
 * Mocks are used here because this is legacy code that wasn't designed
 * for easy testability. Abstracting it further wasn't worth the time.
 */

declare(strict_types=1);

if (! function_exists('add_action')) {
    function add_action(string $hook, string|callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
    }
}

if (! function_exists('add_filter')) {
    function add_filter(string $hook, string|callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
    }
}

if (! function_exists('apply_filters')) {
    function apply_filters(string $hook, mixed ...$args): mixed
    {
        return $args[0] ?? null;
    }
}

if (! function_exists('plugin_dir_url')) {
    function plugin_dir_url(string $file): string
    {
        return 'https://example.com/wp-content/plugins/test/';
    }
}

if (! function_exists('is_admin')) {
    function is_admin(): bool
    {
        return false;
    }
}

if (! function_exists('get_term_meta')) {
    function get_term_meta(int $termId, string $key, bool $single = false): mixed
    {
        return false;
    }
}

if (! function_exists('get_taxonomies')) {
    function get_taxonomies(array $args = []): array
    {
        return [];
    }
}

require_once __DIR__ . '/../vendor/autoload.php';
