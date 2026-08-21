<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Module;

use MunicipioModularityNavigation\ChildrenItems;
use MunicipioModularityNavigation\GridPresentation;
use MunicipioModularityNavigation\ManualItems;
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
        $this->description = __(
            'Outputs links from menus, manual selections, or child pages.',
            'modularity-navigation',
        );
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
        $items = match ($source) {
            'menu' => (new MenuItems())->fromMenu((string) ($fields['mod_navigation_menu'] ?? '')),
            'manual' => (new ManualItems())->fromFields($fields['mod_navigation_items'] ?? null),
            'children' => (new ChildrenItems())->fromPost($this->currentPostId()),
            default => [],
        };

        if (!in_array($format, ['grid', 'buttons', 'list', 'inline', 'bar'], true)) {
            $items = [];
        }

        $gridStyle = get_theme_mod('mod_navigation_grid_style', 'blocks');
        $gridPresentationView = $format === 'grid'
            ? apply_filters(
                'MunicipioModularityNavigation/gridPresentationView',
                null,
                (int) $this->ID,
                $items,
                $gridStyle,
            )
            : null;

        return [
            'format' => $format,
            'source' => $source,
            'items' => $items,
            'gridStyle' => $gridStyle,
            'gridPresentationViews' => (new GridPresentation())->viewCandidates($gridPresentationView),
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

    /**
     * Municipio owns page-context resolution for frontend, archive, and asynchronous module
     * rendering. The WordPress fallback keeps the plugin fail-closed on compatible installs
     * where that helper has not loaded yet.
     */
    private function currentPostId(): int
    {
        if (class_exists(\Municipio\Helper\CurrentPostId::class)) {
            return (int) \Municipio\Helper\CurrentPostId::get();
        }

        return function_exists('get_queried_object_id') ? (int) get_queried_object_id() : 0;
    }
}
