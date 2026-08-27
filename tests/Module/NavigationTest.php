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
        $GLOBALS['modularity_navigation_test_children'] = [];
        $GLOBALS['modularity_navigation_test_siblings'] = [];
        $GLOBALS['modularity_navigation_test_current_post_id'] = 0;
        $GLOBALS['modularity_navigation_test_permalinks'] = [];
        $GLOBALS['modularity_navigation_test_post_queries'] = [];
        $GLOBALS['modularity_navigation_test_url_post_ids'] = [];
        $GLOBALS['modularity_navigation_test_meta_writes'] = [];
        $GLOBALS['modularity_navigation_test_filters'] = [];
        $GLOBALS['modularity_navigation_test_excerpts'] = [];
        $GLOBALS['modularity_navigation_test_images'] = [];
        $GLOBALS['modularity_navigation_test_prepared_posts'] = [];
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
        $GLOBALS['modularity_navigation_test_filters']['MunicipioModularityNavigation/gridPresentationView'][] =
            static fn(mixed $view, int $moduleId, array $items): string => 'nora-navigation-grid';

        $data = (new Navigation())->data();

        self::assertSame('grid', $data['format']);
        self::assertSame('menu', $data['source']);
        self::assertSame('blocks', $data['gridStyle']);
        self::assertCount(1, $data['items']);
        self::assertSame('Omsorg och stöd', $data['items'][0]['title']);
        self::assertSame('Ekonomi, familj och omsorg', $data['items'][0]['description']);
        self::assertFalse($data['showIfEmpty']);
        self::assertSame(['nora-navigation-grid', 'navigation-grid'], $data['gridPresentationViews']);
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
        yield 'grid with child pages' => ['grid', 'children'];
        yield 'buttons with child pages' => ['buttons', 'children'];
        yield 'buttons with sibling pages' => ['buttons', 'siblings'];
        yield 'list with menu' => ['list', 'menu'];
        yield 'list with manual items' => ['list', 'manual'];
        yield 'list with child pages' => ['list', 'children'];
        yield 'cards with menu' => ['cards', 'menu'];
        yield 'cards with manual items' => ['cards', 'manual'];
        yield 'cards with child pages' => ['cards', 'children'];
        yield 'cards with sibling pages' => ['cards', 'siblings'];
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
        $GLOBALS['modularity_navigation_test_current_post_id'] = 18;
        $GLOBALS['modularity_navigation_test_posts'][18] = new \WP_Post(18, 'Utbildning och barnomsorg');
        $GLOBALS['modularity_navigation_test_children'][18] = [new \WP_Post(32, 'Child page')];
        $GLOBALS['modularity_navigation_test_siblings'][0] = [new \WP_Post(33, 'Sibling page')];
        $GLOBALS['modularity_navigation_test_permalinks'][32] = 'https://example.test/child/';
        $GLOBALS['modularity_navigation_test_permalinks'][33] = 'https://example.test/sibling/';

        $data = (new Navigation())->data();

        self::assertSame($format, $data['format']);
        self::assertSame($source, $data['source']);
        self::assertCount(1, $data['items']);
        self::assertSame(
            match ($source) {
                'menu' => 'Menu link',
                'manual' => 'Manual link',
                'children' => 'Child page',
                'siblings' => 'Sibling page',
            },
            $data['items'][0]['title'],
        );
    }

    public function testItReadsHoorsImportedListOfChildPagesWithoutResavingTheModule(): void
    {
        $GLOBALS['modularity_navigation_test_fields'] = [
            'mod_navigation_format' => 'list',
            'mod_navigation_source' => 'children',
        ];
        $GLOBALS['modularity_navigation_test_current_post_id'] = 18;
        $GLOBALS['modularity_navigation_test_posts'][18] = new \WP_Post(18, 'Utbildning och barnomsorg');

        $children = [
            [32,    'Förskola'],
            [33,    'Grundskola'],
            [64299, 'Skolskjuts'],
            [35,    'Anpassad grundskola'],
            [90,    'E-tjänster och blanketter för skola, förskola och fritidshem'],
            [19468, 'Måltider på förskolor och skolor'],
            [34,    'Gymnasium'],
            [3952,  'Höörs Lärcentrum vuxenutbildningen'],
            [39,    'Elevhälsa och särskilt stöd'],
            [36,    'Kulturskola'],
            [81,    'Pedagogisk omsorg på obekväm arbetstid'],
            [62206, 'Synpunkter eller klagomål på förskola eller skola'],
            [52,    'Digitalisering'],
            [153,   'Olycksfallsförsäkring'],
        ];

        foreach ($children as [$postId, $title]) {
            $GLOBALS['modularity_navigation_test_children'][18][] = new \WP_Post($postId, $title);
            $GLOBALS['modularity_navigation_test_permalinks'][$postId] = "https://example.test/{$postId}/";
        }

        $GLOBALS['modularity_navigation_test_acf'][64299]['custom_menu_title'] = 'Skolskjuts för elever';
        $GLOBALS['modularity_navigation_test_acf'][64299]['page_navigation_description'] = 'Ansök och läs reglerna';
        $GLOBALS['modularity_navigation_test_acf'][64299]['page_navigation_icon'] = ['name' => 'directions_bus'];

        $data = (new Navigation())->data();

        self::assertSame('list', $data['format']);
        self::assertSame('children', $data['source']);
        self::assertSame(
            [
                'Förskola',
                'Grundskola',
                'Skolskjuts för elever',
                'Anpassad grundskola',
                'E-tjänster och blanketter för skola, förskola och fritidshem',
                'Måltider på förskolor och skolor',
                'Gymnasium',
                'Höörs Lärcentrum vuxenutbildningen',
                'Elevhälsa och särskilt stöd',
                'Kulturskola',
                'Pedagogisk omsorg på obekväm arbetstid',
                'Synpunkter eller klagomål på förskola eller skola',
                'Digitalisering',
                'Olycksfallsförsäkring',
            ],
            array_column($data['items'], 'title'),
        );
        self::assertSame('Ansök och läs reglerna', $data['items'][2]['description']);
        self::assertSame('directions_bus', $data['items'][2]['icon']);
        self::assertSame('default', $data['items'][2]['buttonVariant']);
        self::assertSame('', $data['items'][2]['target']);
        self::assertSame(
            [
                'post_parent' => 18,
                'post_type' => 'page',
                'nopaging' => true,
                'post_status' => 'publish',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'meta_query' => [
                    'relation' => 'OR',
                    ['key' => 'hide_in_menu', 'value' => '1', 'compare' => '!='],
                    ['key' => 'hide_in_menu', 'compare' => 'NOT EXISTS'],
                ],
            ],
            $GLOBALS['modularity_navigation_test_post_queries'][0],
        );
        self::assertSame([], $GLOBALS['modularity_navigation_test_meta_writes']);
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
