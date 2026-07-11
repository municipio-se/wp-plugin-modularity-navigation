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
        self::assertSame(['grid' => 'Grid'], $fields['mod_navigation_format']['choices']);
        self::assertSame(['menu' => 'Menu'], $fields['mod_navigation_source']['choices']);
        self::assertSame(['sidebarmenu' => 'SidebarMenu'], $fields['mod_navigation_menu']['choices']);
    }
}
