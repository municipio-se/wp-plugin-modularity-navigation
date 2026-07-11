<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests\Module;

use MunicipioModularityNavigation\Module\Navigation;
use PHPUnit\Framework\TestCase;

final class NavigationTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['modularity_navigation_test_fields'] = [];
        $GLOBALS['modularity_navigation_test_theme_mods'] = [];
        $GLOBALS['modularity_navigation_test_menus'] = [];
        $GLOBALS['modularity_navigation_test_acf'] = [];
        $GLOBALS['modularity_navigation_test_styles'] = [];
    }

    public function testItMapsTheImportedGridMenuWithoutChangingStoredValues(): void
    {
        $GLOBALS['modularity_navigation_test_fields'] = [
            'mod_navigation_format' => 'grid',
            'mod_navigation_source' => 'menu',
            'mod_navigation_menu' => 'sidebarmenu',
            'mod_navigation_show_if_empty' => '0',
        ];
        $GLOBALS['modularity_navigation_test_theme_mods']['mod_navigation_grid_style'] = 'blocks';
        $GLOBALS['modularity_navigation_test_menus']['sidebarmenu'] = [
            (object) [
                'menu_item_parent' => '0',
                'title' => 'Omsorg och stöd',
                'url' => 'https://example.test/omsorg-stod/',
                'description' => '',
                'target' => '',
                'object_id' => '101',
            ],
            (object) [
                'menu_item_parent' => '42',
                'title' => 'Nested item',
                'url' => 'https://example.test/nested/',
                'description' => '',
                'target' => '',
                'object_id' => '102',
            ],
        ];
        $GLOBALS['modularity_navigation_test_acf'][101]['page_navigation_description'] = 'Ekonomi, familj och omsorg';

        $data = (new Navigation())->data();

        self::assertSame('grid', $data['format']);
        self::assertSame('menu', $data['source']);
        self::assertSame('blocks', $data['gridStyle']);
        self::assertCount(1, $data['items']);
        self::assertSame('Omsorg och stöd', $data['items'][0]['title']);
        self::assertSame('Ekonomi, familj och omsorg', $data['items'][0]['description']);
        self::assertFalse($data['showIfEmpty']);
    }

    public function testItReturnsNoItemsForFormatsOutsideTheFirstPort(): void
    {
        $GLOBALS['modularity_navigation_test_fields'] = [
            'mod_navigation_format' => 'buttons',
            'mod_navigation_source' => 'manual',
            'mod_navigation_menu' => 'sidebarmenu',
        ];

        self::assertSame([], (new Navigation())->data()['items']);
    }

    public function testItPreservesTheConfiguredEmptyState(): void
    {
        $GLOBALS['modularity_navigation_test_fields'] = [
            'mod_navigation_format' => 'grid',
            'mod_navigation_source' => 'menu',
            'mod_navigation_menu' => 'missing',
            'mod_navigation_show_if_empty' => '1',
            'mod_navigation_empty_message' => '<p>No links yet.</p>',
        ];

        $data = (new Navigation())->data();

        self::assertSame([], $data['items']);
        self::assertTrue($data['showIfEmpty']);
        self::assertSame('<p>No links yet.</p>', $data['emptyMessage']);
    }

    public function testItEnqueuesAPluginOwnedStylesheet(): void
    {
        (new Navigation())->style();

        self::assertSame('modularity-navigation', $GLOBALS['modularity_navigation_test_styles'][0][0]);
        self::assertSame(
            'https://example.test/wp-content/plugins/modularity-navigation/assets/css/navigation.css',
            $GLOBALS['modularity_navigation_test_styles'][0][1],
        );
    }
}
