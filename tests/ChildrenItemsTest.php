<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use MunicipioModularityNavigation\ChildrenItems;
use PHPUnit\Framework\TestCase;

final class ChildrenItemsTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['modularity_navigation_test_posts'] = [];
        $GLOBALS['modularity_navigation_test_children'] = [];
        $GLOBALS['modularity_navigation_test_permalinks'] = [];
        $GLOBALS['modularity_navigation_test_acf'] = [];
        $GLOBALS['modularity_navigation_test_post_queries'] = [];
        $GLOBALS['modularity_navigation_test_post_meta'] = [];
    }

    public function testItReturnsNoItemsWhenTheCurrentPostIsUnavailable(): void
    {
        self::assertSame([], (new ChildrenItems())->fromPost(0));
        self::assertSame([], $GLOBALS['modularity_navigation_test_post_queries']);
    }

    public function testItSkipsMalformedChildrenAndEmptyLinksWithoutWarnings(): void
    {
        $GLOBALS['modularity_navigation_test_posts'][18] = new \WP_Post(18, 'Parent');
        $GLOBALS['modularity_navigation_test_children'][18] = [
            null,
            new \WP_Post(32, ''),
            new \WP_Post(33, 'Missing permalink'),
            new \WP_Post(34, 'Valid child'),
        ];
        $GLOBALS['modularity_navigation_test_permalinks'][32] = 'https://example.test/title-from-acf/';
        $GLOBALS['modularity_navigation_test_permalinks'][34] = 'https://example.test/valid/';
        $GLOBALS['modularity_navigation_test_acf'][32]['custom_menu_title'] = 'Title from ACF';

        $items = (new ChildrenItems())->fromPost(18);

        self::assertSame(['Title from ACF', 'Valid child'], array_column($items, 'title'));
        self::assertSame(
            ['https://example.test/title-from-acf/', 'https://example.test/valid/'],
            array_column($items, 'href'),
        );
    }

    public function testItPreservesVisibilityAndChildOrdering(): void
    {
        $GLOBALS['modularity_navigation_test_posts'][18] = new \WP_Post(18, 'Parent');
        $values = [
            19 => [],
            20 => ['0'],
            21 => [''],
            22 => ['1'],
            23 => ['1', '1'],
            24 => ['1', '0'],
            25 => ['0', '1'],
        ];
        foreach ($values as $id => $metadata) {
            $GLOBALS['modularity_navigation_test_children'][18][] = new \WP_Post($id, "Page $id");
            $GLOBALS['modularity_navigation_test_post_meta'][$id]['hide_in_menu'] = $metadata;
            $GLOBALS['modularity_navigation_test_permalinks'][$id] = "https://example.test/$id/";
        }
        self::assertSame([19, 20, 21, 24, 25], array_column((new ChildrenItems())->fromPost(18), 'postId'));
        self::assertSame(
            [
                'post_parent' => 18,
                'post_type' => 'page',
                'nopaging' => true,
                'post_status' => 'publish',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'update_post_meta_cache' => true,
            ],
            $GLOBALS['modularity_navigation_test_post_queries'][0],
        );
    }
}
