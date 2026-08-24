<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use MunicipioModularityNavigation\SiblingItems;
use PHPUnit\Framework\TestCase;

final class SiblingItemsTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['modularity_navigation_test_posts'] = [];
        $GLOBALS['modularity_navigation_test_siblings'] = [];
        $GLOBALS['modularity_navigation_test_permalinks'] = [];
        $GLOBALS['modularity_navigation_test_acf'] = [];
        $GLOBALS['modularity_navigation_test_post_queries'] = [];
    }

    public function testItReturnsNoItemsWhenTheCurrentPostIsUnavailable(): void
    {
        self::assertSame([], (new SiblingItems())->fromPost(0));
        self::assertSame([], $GLOBALS['modularity_navigation_test_post_queries']);
    }

    public function testItUsesTheLtsSiblingScopeAndOrdering(): void
    {
        $GLOBALS['modularity_navigation_test_posts'][18] = new \WP_Post(18, 'Kontakta oss', 'page', 3, 7);
        $GLOBALS['modularity_navigation_test_siblings'][7] = [
            new \WP_Post(21, 'Beslut'),
            new \WP_Post(20, 'Avgifter'),
            new \WP_Post(19, 'Ekonomi'),
        ];
        $GLOBALS['modularity_navigation_test_permalinks'] = [
            21 => 'https://example.test/beslut/',
            20 => 'https://example.test/avgifter/',
            19 => 'https://example.test/ekonomi/',
        ];

        $items = (new SiblingItems())->fromPost(18);

        self::assertSame(['Beslut', 'Avgifter', 'Ekonomi'], array_column($items, 'title'));
        self::assertSame(
            [
                'post_parent' => 7,
                'post_type' => 'page',
                'post__not_in' => [18],
                'nopaging' => true,
                'post_status' => 'publish',
                'orderby' => 'menu_order',
                'order' => 'DESC',
                'meta_query' => [
                    'relation' => 'OR',
                    ['key' => 'hide_in_menu', 'value' => '1', 'compare' => '!='],
                    ['key' => 'hide_in_menu', 'compare' => 'NOT EXISTS'],
                ],
            ],
            $GLOBALS['modularity_navigation_test_post_queries'][0],
        );
    }
}
