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
        $GLOBALS['modularity_navigation_test_posts'] = [];
        $GLOBALS['modularity_navigation_test_url_post_ids'] = [];
        $GLOBALS['modularity_navigation_test_meta_writes'] = [];
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

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function supportedCombinations(): iterable
    {
        yield 'grid with menu' => ['grid', 'menu'];
        yield 'grid with manual items' => ['grid', 'manual'];
        yield 'buttons with menu' => ['buttons', 'menu'];
        yield 'buttons with manual items' => ['buttons', 'manual'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('supportedCombinations')]
    public function testItKeepsFormatAndSourceAsIndependentCapabilities(string $format, string $source): void
    {
        $GLOBALS['modularity_navigation_test_fields'] = [
            'mod_navigation_format' => $format,
            'mod_navigation_source' => $source,
            'mod_navigation_menu' => 'sidebarmenu',
            'mod_navigation_items' => [
                [
                    'link' => [
                        'title' => 'Manual link',
                        'url' => 'https://example.test/manual/',
                        'target' => '_blank',
                    ],
                    'button_variant' => 'primary',
                    'icon' => 'mobile_friendly',
                ],
            ],
        ];
        $GLOBALS['modularity_navigation_test_menus']['sidebarmenu'] = [
            (object) [
                'menu_item_parent' => '0',
                'title' => 'Menu link',
                'url' => 'https://example.test/menu/',
                'description' => '',
                'target' => '',
                'object_id' => '0',
            ],
        ];

        $data = (new Navigation())->data();

        self::assertSame($format, $data['format']);
        self::assertSame($source, $data['source']);
        self::assertCount(1, $data['items']);
        self::assertSame($source === 'menu' ? 'Menu link' : 'Manual link', $data['items'][0]['title']);
    }

    public function testItReadsHoorsImportedButtonsWithoutResavingTheModule(): void
    {
        $GLOBALS['modularity_navigation_test_fields'] = [
            'mod_navigation_format' => 'buttons',
            'mod_navigation_source' => 'manual',
            'mod_navigation_items' => [
                $this->manualItem(
                    'SchoolSoft',
                    'https://sms.schoolsoft.se/hoor/etjanst/#/',
                    '_blank',
                    'primary',
                    'mobile_friendly',
                ),
                $this->manualItem(
                    'InfoMentor',
                    'https://infomentor.se/swedish/production/mentor/',
                    '_blank',
                    'primary',
                    'mobile_friendly',
                ),
                $this->manualItem(
                    'Fler e-tjänster för förskola, skola och utbildning',
                    'https://etjanster.hoor.se/oversikt',
                    '_blank',
                    'primary',
                    'mobile_friendly',
                ),
                $this->manualItem(
                    'Matsedel för förskola och skola',
                    'https://www.hoor.se/utbildning-barnomsorg/mat-och-lunch/meny-for-forskola-och-skola/',
                    '',
                    'secondary',
                    '',
                ),
                $this->manualItem(
                    'Skolskjuts',
                    'https://www.hoor.se/utbildning-barnomsorg/skolskjuts-grundskola/',
                    '',
                    'secondary',
                    '',
                ),
                $this->manualItem(
                    'Läsårstider och lov',
                    'https://www.hoor.se/utbildning-barnomsorg/grundskola/lasarstider/',
                    '_blank',
                    'secondary',
                    '',
                ),
            ],
        ];

        $items = (new Navigation())->data()['items'];

        self::assertSame(
            [
                'SchoolSoft',
                'InfoMentor',
                'Fler e-tjänster för förskola, skola och utbildning',
                'Matsedel för förskola och skola',
                'Skolskjuts',
                'Läsårstider och lov',
            ],
            array_column($items, 'title'),
        );
        self::assertSame(
            ['primary', 'primary', 'primary', 'secondary', 'secondary', 'secondary'],
            array_column($items, 'buttonVariant'),
        );
        self::assertSame(
            ['mobile_friendly', 'mobile_friendly', 'mobile_friendly', '', '', ''],
            array_column($items, 'icon'),
        );
        self::assertSame(['_blank', '_blank', '_blank', '', '', '_blank'], array_column($items, 'target'));
        self::assertSame([], $GLOBALS['modularity_navigation_test_meta_writes']);
    }

    public function testItSkipsMalformedManualItemsWithoutWarningsOrEmptyLinks(): void
    {
        $GLOBALS['modularity_navigation_test_fields'] = [
            'mod_navigation_format' => 'buttons',
            'mod_navigation_source' => 'manual',
            'mod_navigation_items' => [
                null,
                ['link' => 'not-an-array'],
                ['link' => ['title' => 'Missing URL']],
                ['link' => ['title' => '', 'url' => 'https://example.test/missing-title/']],
                [
                    'link' => [
                        'title' => 'Valid link',
                        'url' => 'https://example.test/valid/',
                        'target' => 'unsafe-frame',
                    ],
                    'button_variant' => 'invented',
                    'icon' => ['unexpected' => 'shape'],
                ],
            ],
        ];

        $items = (new Navigation())->data()['items'];

        self::assertCount(1, $items);
        self::assertSame('Valid link', $items[0]['title']);
        self::assertSame('', $items[0]['target']);
        self::assertSame('default', $items[0]['buttonVariant']);
        self::assertSame('', $items[0]['icon']);
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

    /**
     * @return array{link: array{title: string, url: string, target: string}, button_variant: string, icon: string}
     */
    private function manualItem(string $title, string $url, string $target, string $buttonVariant, string $icon): array
    {
        return [
            'link' => ['title' => $title, 'url' => $url, 'target' => $target],
            'button_variant' => $buttonVariant,
            'icon' => $icon,
        ];
    }
}
