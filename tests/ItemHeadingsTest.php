<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use MunicipioModularityNavigation\ItemHeadings;
use PHPUnit\Framework\TestCase;

final class ItemHeadingsTest extends TestCase
{
    public function testOnlyOwnedHeadingTagsChangeAndSourceBytesSurvive(): void
    {
        $html =
            '<ul><li><a class="c-card" href="/?a=1&amp;b=2" target="_blank" rel="noopener">'
            . '<svg viewBox="0 0 1 1"><path d="M0 0"/></svg>'
            . '<div><h2 id="title" class="c-card__heading c-typography__variant--h3">Åäö &amp; <em>Text</em></h2></div>'
            . '<div class="c-card__body-content"><h2 class="c-card__heading">Content</h2></div>'
            . '<div class="c-card"><h2 class="c-card__heading">Nested</h2></div>'
            . '</a></li></ul>';
        $expected = str_replace(
            '<h2 id="title" class="c-card__heading c-typography__variant--h3">Åäö &amp; <em>Text</em></h2>',
            '<h3 id="title" class="c-card__heading c-typography__variant--h3">Åäö &amp; <em>Text</em></h3>',
            $html,
        );
        self::assertSame($expected, ItemHeadings::transform($html, 'h3'));
        self::assertSame($expected, ItemHeadings::transform($expected, 'h3'));
        self::assertSame($html, ItemHeadings::transform($expected, 'h2'));
    }

    public function testMalformedOrUnownedMarkupIsPreserved(): void
    {
        foreach ([
            '',
            '<h2 class="c-card__heading">Outside</h2>',
            '<div class="c-card"><h2 class="c-card__heading">Missing close</div>',
            '<div class="c-card"><h2 class="c-card__heading">Wrong close</h3></div>',
            '<div class="c-card"><h2 class="c-card__heading"><h3>Nested</h3></h2></div>',
        ] as $html) {
            self::assertSame($html, ItemHeadings::transform($html, 'h3'));
        }
    }

    public function testTreePreservesTheRenderedTypographyAndChildLinks(): void
    {
        $html =
            '<ul class="mod-navigation-tree"><li>'
            . '<h1 class="mod-navigation-tree__heading c-typography__variant--h1"><a href="/">Tree</a></h1>'
            . '<ul><li><a href="/child">Child</a></li></ul></li></ul>';
        $expected = str_replace(['<h1 ', '</h1>'], ['<h2 ', '</h2>'], $html);
        self::assertSame($expected, ItemHeadings::transform($html, 'h2', 'tree'));
        self::assertSame($html, ItemHeadings::transform($html, 'h2', 'cards'));
        self::assertSame($html, ItemHeadings::transform($html, 'h4', 'tree'));
        self::assertSame($html, ItemHeadings::transform($html, 'h2', 'unknown'));
    }
}
