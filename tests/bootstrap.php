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

namespace Municipio\Helper {
    class CurrentPostId
    {
        public static function get(): int
        {
            return $GLOBALS['modularity_navigation_test_current_post_id'] ?? 0;
        }
    }

    class Post
    {
        public static function preparePostObjectArchive(\WP_Post $post): object
        {
            $GLOBALS['modularity_navigation_test_prepared_posts'][] = $post->ID;

            return new \ModularityNavigationTestPostObject(
                $GLOBALS['modularity_navigation_test_excerpts'][$post->ID] ?? '',
                $GLOBALS['modularity_navigation_test_images'][$post->ID] ?? null,
            );
        }
    }
}

namespace {
    class ModularityNavigationTestPostObject
    {
        public function __construct(
            public string $excerptShort,
            private mixed $image,
        ) {}

        public function getImage(): mixed
        {
            return $this->image;
        }
    }

    class WP_Post
    {
        public function __construct(
            public int $ID,
            public string $post_title = '',
            public string $post_type = 'page',
            public int $menu_order = 0,
            public int $post_parent = 0,
        ) {}
    }

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

    function apply_filters(string $hook, mixed $value, mixed ...$args): mixed
    {
        foreach ($GLOBALS['modularity_navigation_test_filters'][$hook] ?? [] as $callback) {
            $value = $callback($value, ...$args);
        }

        return $value;
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

    function url_to_postid(string $url): int
    {
        return $GLOBALS['modularity_navigation_test_url_post_ids'][$url] ?? 0;
    }

    function get_post(int $postId): ?WP_Post
    {
        return $GLOBALS['modularity_navigation_test_posts'][$postId] ?? null;
    }

    function get_posts(array $args): array|false
    {
        $GLOBALS['modularity_navigation_test_post_queries'][] = $args;

        if (isset($args['post__not_in'])) {
            return $GLOBALS['modularity_navigation_test_siblings'][$args['post_parent'] ?? 0] ?? false;
        }

        return $GLOBALS['modularity_navigation_test_children'][$args['post_parent'] ?? 0] ?? false;
    }

    function get_permalink(int $postId): string|false
    {
        return $GLOBALS['modularity_navigation_test_permalinks'][$postId] ?? false;
    }

    function get_queried_object_id(): int
    {
        return $GLOBALS['modularity_navigation_test_queried_object_id'] ?? 0;
    }

    function update_post_meta(...$args): void
    {
        $GLOBALS['modularity_navigation_test_meta_writes'][] = $args;
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
