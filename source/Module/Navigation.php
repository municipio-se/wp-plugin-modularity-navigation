<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Module;

use MunicipioModularityNavigation\MenuItems;

final class Navigation extends \Modularity\Module
{
    public $slug = 'navigation';
    public $supports = [];
    public $cacheTtl = 0;

    /**
     * The current Modularity asset manager resolves reflected symlink targets outside
     * wp-content/plugins and rejects that context. This module owns its single stylesheet, so
     * bypassing the unused manager preserves both normal Composer installs and local symlinks.
     */
    public function __construct(
        ?\WP_Post $post = null,
        array $args = [],
        ?\WpUtilService\WpUtilServiceInterface $wpUtilService = null,
    ) {
        parent::__construct($post, $args, null);
    }

    public function init(): void
    {
        $this->nameSingular = __('Navigation', 'modularity-navigation');
        $this->namePlural = __('Navigation modules', 'modularity-navigation');
        $this->description = __('Outputs links from a selected WordPress menu.', 'modularity-navigation');
        $this->templateDir = MODULARITY_NAVIGATION_PATH . 'views/';
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        $fields = $this->getFields();
        $format = $fields['mod_navigation_format'] ?? '';
        $source = $fields['mod_navigation_source'] ?? '';
        $items =
            $format === 'grid' && $source === 'menu'
                ? (new MenuItems())->fromMenu((string) ($fields['mod_navigation_menu'] ?? ''))
                : [];

        return [
            'format' => $format,
            'source' => $source,
            'items' => $items,
            'gridStyle' => get_theme_mod('mod_navigation_grid_style', 'blocks'),
            'showIfEmpty' => in_array($fields['mod_navigation_show_if_empty'] ?? null, [1, '1', true], true),
            'emptyMessage' => (string) ($fields['mod_navigation_empty_message'] ?? ''),
        ];
    }

    public function style(): void
    {
        wp_enqueue_style(
            'modularity-navigation',
            MODULARITY_NAVIGATION_URL . 'assets/css/navigation.css',
            [],
            MODULARITY_NAVIGATION_VERSION,
        );
    }
}
