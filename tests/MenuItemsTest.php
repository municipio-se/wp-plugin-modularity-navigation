<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use MunicipioModularityNavigation\MenuItems;
use PHPUnit\Framework\TestCase;

final class MenuItemsTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['modularity_navigation_test_menus'] = [];
        $GLOBALS['modularity_navigation_test_acf'] = [];
    }

    public function testItReturnsNoItemsForAnEmptySlug(): void
    {
        self::assertSame([], (new MenuItems())->fromMenu(''));
    }

    public function testItResolvesTheMenuItemIconAndSkipsNestedItems(): void
    {
        $GLOBALS['modularity_navigation_test_menus']['startsida'] = [
            (object) [
                'ID' => 100,
                'menu_item_parent' => 0,
                'title' => 'Utbildning',
                'url' => 'https://example.test/utbildning/',
                'target' => '',
                'description' => '',
                'object_id' => 10,
            ],
            (object) [
                // Nested item — belongs to a level this format does not render.
                'ID' => 101,
                'menu_item_parent' => 5,
                'title' => 'Nested',
                'url' => 'https://example.test/nested/',
                'object_id' => 11,
            ],
            (object) [
                'ID' => 102,
                'menu_item_parent' => 0,
                'title' => 'Utan ikon',
                'url' => 'https://example.test/utan-ikon/',
                'object_id' => 12,
            ],
        ];
        // Icon lives on the menu item itself, not the connected page.
        $GLOBALS['modularity_navigation_test_acf'][100]['menu_item_icon'] = 'school';

        $items = (new MenuItems())->fromMenu('startsida');

        self::assertSame(['Utbildning', 'Utan ikon'], array_column($items, 'title'));
        self::assertSame(['school', ''], array_column($items, 'icon'));
    }

    public function testItFallsBackToTheLegacyIconFieldAndAcceptsArrayShapes(): void
    {
        $GLOBALS['modularity_navigation_test_menus']['m'] = [
            (object) [
                'ID' => 200,
                'menu_item_parent' => 0,
                'title' => 'A',
                'url' => 'https://example.test/a/',
                'object_id' => 20,
            ],
        ];
        // No `menu_item_icon`; legacy `icon` field (array-shaped) is used.
        $GLOBALS['modularity_navigation_test_acf'][200]['icon'] = ['name' => 'call'];

        $items = (new MenuItems())->fromMenu('m');

        self::assertSame('call', $items[0]['icon']);
    }
}
