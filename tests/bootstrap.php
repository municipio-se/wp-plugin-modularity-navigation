<?php

declare(strict_types=1);

namespace Modularity {
    class Module
    {
        public $ID = 81858;
        public $slug = '';
        public $supports = [];
        public $cacheTtl = 0;
        public $nameSingular = '';
        public $namePlural = '';
        public $description = '';
        public $templateDir = false;

        public function __construct(mixed $post = null, array $args = [], mixed $wpUtilService = null)
        {
            $this->init();
        }

        public function init(): void {}

        protected function getFields(): array
        {
            return $GLOBALS['modularity_navigation_test_fields'] ?? [];
        }
    }
}

namespace {
    define('ABSPATH', __DIR__);
    define('MODULARITY_NAVIGATION_PATH', dirname(__DIR__) . '/');
    define('MODULARITY_NAVIGATION_URL', 'https://example.test/wp-content/plugins/modularity-navigation/');
    define('MODULARITY_NAVIGATION_VERSION', '0.1.0');

    function __(string $text): string
    {
        return $text;
    }

    function _x(string $text): string
    {
        return $text;
    }

    function get_theme_mod(string $name, mixed $default = false): mixed
    {
        return $GLOBALS['modularity_navigation_test_theme_mods'][$name] ?? $default;
    }

    function wp_get_nav_menu_items(string $slug): array|false
    {
        return $GLOBALS['modularity_navigation_test_menus'][$slug] ?? false;
    }

    function wp_get_nav_menus(): array
    {
        return $GLOBALS['modularity_navigation_test_menu_objects'] ?? [];
    }

    function get_field(string $name, int $postId): mixed
    {
        return $GLOBALS['modularity_navigation_test_acf'][$postId][$name] ?? null;
    }

    function wp_enqueue_style(...$args): void
    {
        $GLOBALS['modularity_navigation_test_styles'][] = $args;
    }

    function acf_add_local_field_group(array $group): void
    {
        $GLOBALS['modularity_navigation_test_field_groups'][] = $group;
    }

    require dirname(__DIR__) . '/vendor/autoload.php';
}
