<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use MunicipioModularityNavigation\Module\Navigation;
use MunicipioModularityNavigation\TreeMenuItems;
use PHPUnit\Framework\TestCase;

final class TreeTest extends TestCase
{
    protected function setUp(): void
    {
        foreach ([
            'fields',
            'theme_mods',
            'menus',
            'acf',
            'posts',
            'children',
            'siblings',
            'permalinks',
            'post_queries',
            'post_meta',
            'url_post_ids',
            'meta_writes',
            'filters',
            'thumbnail_ids',
            'image_requests',
        ] as $key) {
            $GLOBALS['modularity_navigation_test_' . $key] = [];
        }
        $GLOBALS['modularity_navigation_test_current_post_id'] = 10;
        $GLOBALS['modularity_navigation_test_fields'] = [
            'mod_navigation_format' => 'tree',
            'mod_navigation_source' => 'menu',
            'mod_navigation_menu' => 'test',
        ];
    }

    public function testMenuUsesMenuParentsAndTwoLevelsWithoutPromotingOrphans(): void
    {
        $GLOBALS['modularity_navigation_test_menus']['test'] = [
            $this->menu(2, 1),
            $this->menu(3, 2),
            $this->menu(4, 99),
            $this->menu(1, 0),
            $this->menu(5, 0),
            $this->menu(6, 1),
            $this->menu(7, 7),
            null,
            (object) ['ID' => 8],
        ];
        $GLOBALS['modularity_navigation_test_fields']['mod_navigation_depth'] = 99;
        $data = (new Navigation())->data();
        self::assertSame([1, 5], array_column($data['items'], 'menuId'));
        self::assertSame([2, 6], array_column($data['items'][0]['children'], 'menuId'));
        self::assertArrayNotHasKey('children', $data['items'][0]['children'][0]);
        self::assertSame('_blank', $data['items'][0]['children'][0]['target']);
        self::assertSame([], $data['items'][1]['children']);
        self::assertSame([], $GLOBALS['modularity_navigation_test_meta_writes']);
        self::assertSame(99, $GLOBALS['modularity_navigation_test_fields']['mod_navigation_depth']);
    }

    public function testMenuDescriptionPrecedencePreservesExplicitEmptyAndRejectsNonPostConnections(): void
    {
        $GLOBALS['modularity_navigation_test_menus']['test'] = [$this->menu(1, 0)];
        $GLOBALS['modularity_navigation_test_acf'][11]['page_navigation_description'] = 'Page';
        $read = static fn() => (new TreeMenuItems())->fromMenu('test')[0]['description'];
        self::assertSame('Page', $read());
        $GLOBALS['modularity_navigation_test_acf'][1]['page_navigation_description'] = '';
        self::assertSame('', $read());
        $GLOBALS['modularity_navigation_test_acf'][1]['page_navigation_description'] = false;
        self::assertSame('', $read());
        $GLOBALS['modularity_navigation_test_acf'][1]['page_navigation_description'] = 'Own';
        self::assertSame('Own', $read());
        $GLOBALS['modularity_navigation_test_menus']['test'][0]->description = 'Menu';
        self::assertSame('Menu', $read());
        unset($GLOBALS['modularity_navigation_test_acf'][1]);
        $GLOBALS['modularity_navigation_test_menus']['test'][0]->description = '';
        $GLOBALS['modularity_navigation_test_menus']['test'][0]->type = 'custom';
        self::assertSame('', $read());
    }

    public function testImagesPreferMenuImageAndOtherwiseUseConnectedPageWithoutPlaceholder(): void
    {
        $GLOBALS['modularity_navigation_test_menus']['test'] = [$this->menu(1, 0)];
        $GLOBALS['modularity_navigation_test_thumbnail_ids'] = [1 => 101, 11 => 111];
        self::assertStringContainsString('image-101', (new Navigation())->data()['items'][0]['treeImage']);
        unset($GLOBALS['modularity_navigation_test_thumbnail_ids'][1]);
        self::assertStringContainsString('image-111', (new Navigation())->data()['items'][0]['treeImage']);
        $GLOBALS['modularity_navigation_test_thumbnail_ids'] = [];
        self::assertSame('', (new Navigation())->data()['items'][0]['treeImage']);
        self::assertSame('', $GLOBALS['modularity_navigation_test_image_requests'][0][2]['alt']);
    }

    public function testOnlyChildrenSourceExpandsAndKeepsVisibilityRulesAtBothLevels(): void
    {
        foreach ([10, 11, 12, 13, 14, 15] as $id) {
            $GLOBALS['modularity_navigation_test_posts'][$id] = new \WP_Post($id, 'Page ' . $id);
            $GLOBALS['modularity_navigation_test_permalinks'][$id] = 'https://example.test/' . $id;
        }
        $posts = $GLOBALS['modularity_navigation_test_posts'];
        $GLOBALS['modularity_navigation_test_children'] = [
            10 => [$posts[11], $posts[12]],
            11 => [$posts[13], $posts[14]],
            13 => [$posts[15]],
        ];
        $GLOBALS['modularity_navigation_test_post_meta'] = [
            12 => ['hide_in_menu' => ['1']],
            14 => ['hide_in_menu' => ['1']],
        ];
        $GLOBALS['modularity_navigation_test_fields']['mod_navigation_source'] = 'children';
        $items = (new Navigation())->data()['items'];
        self::assertSame([11], array_column($items, 'postId'));
        self::assertSame([13], array_column($items[0]['children'], 'postId'));
        self::assertSame([10, 11], array_column($GLOBALS['modularity_navigation_test_post_queries'], 'post_parent'));
        foreach ($GLOBALS['modularity_navigation_test_post_queries'] as $query) {
            self::assertSame('publish', $query['post_status']);
            self::assertSame('ASC', $query['order']);
        }
        $GLOBALS['modularity_navigation_test_fields']['mod_navigation_source'] = 'siblings';
        $GLOBALS['modularity_navigation_test_siblings'][0] = [$posts[11]];
        self::assertSame([], (new Navigation())->data()['items'][0]['children']);
        $GLOBALS['modularity_navigation_test_fields']['mod_navigation_source'] = 'manual';
        $GLOBALS['modularity_navigation_test_url_post_ids']['https://example.test/11'] = 11;
        $GLOBALS['modularity_navigation_test_fields']['mod_navigation_items'] = [['link' => [
            'url' => 'https://example.test/11',
            'title' => 'Chosen',
        ]]];
        self::assertSame([], (new Navigation())->data()['items'][0]['children']);
    }

    public function testStyleFallbackAndEmptyStateDoNotRewriteSavedValues(): void
    {
        foreach ([null, '', 'invalid', [], 'standard', 'highlighted'] as $style) {
            $GLOBALS['modularity_navigation_test_theme_mods']['mod_navigation_tree_style'] = $style;
            $GLOBALS['modularity_navigation_test_fields']['mod_navigation_show_if_empty'] = 1;
            $GLOBALS['modularity_navigation_test_fields']['mod_navigation_empty_message'] = '<p>Empty</p>';
            $data = (new Navigation())->data();
            self::assertSame($style === 'highlighted' ? 'highlighted' : 'standard', $data['treeStyle']);
            self::assertSame([], $data['items']);
            self::assertTrue($data['showIfEmpty']);
            self::assertSame('<p>Empty</p>', $data['emptyMessage']);
            self::assertSame($style, $GLOBALS['modularity_navigation_test_theme_mods']['mod_navigation_tree_style']);
        }
    }

    public function testAllReleasedFormatsStillUseFlatMenuItems(): void
    {
        $GLOBALS['modularity_navigation_test_menus']['test'] = [$this->menu(1, 0), $this->menu(2, 1)];
        foreach (['grid', 'buttons', 'list', 'cards', 'inline', 'bar'] as $format) {
            $GLOBALS['modularity_navigation_test_fields']['mod_navigation_format'] = $format;
            $items = (new Navigation())->data()['items'];
            self::assertCount(1, $items);
            self::assertArrayNotHasKey('children', $items[0]);
        }
    }

    private function menu(int $id, int $parent): object
    {
        return (object) [
            'ID' => $id,
            'menu_item_parent' => $parent,
            'title' => 'Menu ' . $id,
            'url' => 'https://example.test/' . $id,
            'type' => 'post_type',
            'object_id' => $id + 10,
            'target' => '_blank',
            'description' => '',
        ];
    }
}
