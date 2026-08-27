<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use MunicipioModularityNavigation\CardItems;
use PHPUnit\Framework\TestCase;

final class CardItemsTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['modularity_navigation_test_posts'] = [];
        $GLOBALS['modularity_navigation_test_url_post_ids'] = [];
        $GLOBALS['modularity_navigation_test_excerpts'] = [];
        $GLOBALS['modularity_navigation_test_images'] = [];
        $GLOBALS['modularity_navigation_test_prepared_posts'] = [];
    }

    public function testItUsesMunicipiosPostPresentationForLocalLinks(): void
    {
        $image = (object) ['src' => 'https://example.test/image.jpg'];
        $GLOBALS['modularity_navigation_test_posts'][32] = new \WP_Post(32, 'Child page');
        $GLOBALS['modularity_navigation_test_excerpts'][32] = 'Municipios filtrerbara kortutdrag.';
        $GLOBALS['modularity_navigation_test_images'][32] = $image;

        $items = (new CardItems())->fromItems([
            ['title' => 'Navigation title', 'href' => 'https://example.test/child/', 'postId' => 32],
        ]);

        self::assertSame('Navigation title', $items[0]['title']);
        self::assertSame('https://example.test/child/', $items[0]['href']);
        self::assertSame('Municipios filtrerbara kortutdrag.', $items[0]['cardExcerpt']);
        self::assertSame($image, $items[0]['cardImage']);
        self::assertFalse($items[0]['cardHasPlaceholder']);
        self::assertSame([32], $GLOBALS['modularity_navigation_test_prepared_posts']);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function municipioExcerptCases(): iterable
    {
        yield 'manual excerpt stays complete' => [
            'Ett manuellt utdrag ska behållas i sin helhet även när det är längre än en äldre hårdkodad ordgräns.',
        ];
        yield 'content before more stays complete' => [
            'All text som Municipio lämnar tillbaka före more-avgränsningen ska behållas oförändrad.',
        ];
        yield 'filtered automatic excerpt stays authoritative' => [
            'Det här är det filtrerade automatiska kortutdraget.',
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('municipioExcerptCases')]
    public function testItDoesNotAlterMunicipiosExcerptContract(string $excerpt): void
    {
        $GLOBALS['modularity_navigation_test_posts'][63] = new \WP_Post(63, 'Local page');
        $GLOBALS['modularity_navigation_test_excerpts'][63] = $excerpt;

        $items = (new CardItems())->fromItems([
            ['title' => 'Local page', 'href' => 'https://example.test/local/', 'postId' => 63],
        ]);

        self::assertSame($excerpt, $items[0]['cardExcerpt']);
        self::assertSame([63], $GLOBALS['modularity_navigation_test_prepared_posts']);
    }

    public function testItResolvesAUrlAndUsesTheComponentPlaceholderWhenThePostHasNoImage(): void
    {
        $href = 'https://example.test/manual/';
        $GLOBALS['modularity_navigation_test_url_post_ids'][$href] = 44;
        $GLOBALS['modularity_navigation_test_posts'][44] = new \WP_Post(44, 'Manual page');
        $GLOBALS['modularity_navigation_test_excerpts'][44] = 'Manual excerpt.';

        $items = (new CardItems())->fromItems([['title' => 'Manual', 'href' => $href, 'postId' => 0]]);

        self::assertSame('Manual excerpt.', $items[0]['cardExcerpt']);
        self::assertNull($items[0]['cardImage']);
        self::assertTrue($items[0]['cardHasPlaceholder']);
    }

    public function testItKeepsAnExternalLinkWithoutInventingAnExcerpt(): void
    {
        $items = (new CardItems())->fromItems([
            ['title' => 'External', 'href' => 'https://external.example/', 'postId' => 0],
        ]);

        self::assertSame('External', $items[0]['title']);
        self::assertSame('', $items[0]['cardExcerpt']);
        self::assertNull($items[0]['cardImage']);
        self::assertTrue($items[0]['cardHasPlaceholder']);
        self::assertSame([], $GLOBALS['modularity_navigation_test_prepared_posts']);
    }
}
