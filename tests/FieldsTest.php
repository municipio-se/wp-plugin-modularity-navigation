<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use MunicipioModularityNavigation\Fields;
use PHPUnit\Framework\TestCase;

final class FieldsTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['modularity_navigation_test_field_groups'] = [];
        $GLOBALS['modularity_navigation_test_menu_objects'] = [
            (object) ['slug' => 'sidebarmenu', 'name' => 'SidebarMenu'],
        ];
    }

    public function testItRegistersTheLegacyFieldContractForNavigationPosts(): void
    {
        Fields::register();

        $group = $GLOBALS['modularity_navigation_test_field_groups'][0];
        $fields = array_column($group['fields'], null, 'name');

        self::assertSame('group_mod_navigation', $group['key']);
        self::assertSame('mod-navigation', $group['location'][0][0]['value']);
        self::assertSame(
            [
                'grid' => 'Grid',
                'buttons' => 'Buttons',
                'list' => 'List',
                'inline' => 'Inline',
                'bar' => 'Bar',
            ],
            $fields['mod_navigation_format']['choices'],
        );
        self::assertSame(
            [
                'menu' => 'Menu',
                'manual' => 'Manually selected',
                'children' => 'Child pages',
                'siblings' => 'Sibling pages',
            ],
            $fields['mod_navigation_source']['choices'],
        );
        self::assertSame(['sidebarmenu' => 'SidebarMenu'], $fields['mod_navigation_menu']['choices']);
        self::assertSame('menu', $fields['mod_navigation_menu']['conditional_logic'][0][0]['value']);

        $manualItems = $fields['mod_navigation_items'];
        $subFields = array_column($manualItems['sub_fields'], null, 'name');

        self::assertSame('manual', $manualItems['conditional_logic'][0][0]['value']);
        self::assertSame('link', $subFields['link']['type']);
        self::assertSame(
            ['primary' => 'Primary', 'secondary' => 'Secondary', 'default' => 'Default'],
            $subFields['button_variant']['choices'],
        );
        self::assertSame('buttons', $subFields['button_variant']['conditional_logic'][0][0]['value']);
        self::assertSame('icon', $subFields['icon']['type']);
        self::assertSame(
            ['buttons', 'list', 'inline', 'bar'],
            array_column(array_column($subFields['icon']['conditional_logic'], 0), 'value'),
        );
    }
}
