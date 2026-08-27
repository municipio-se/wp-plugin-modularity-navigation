<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    public function testItUsesCurrentMunicipioPresentationForAllFormats(): void
    {
        $view = file_get_contents(dirname(__DIR__) . '/views/navigation.blade.php');

        self::assertIsString($view);
        self::assertStringContainsString("\$format === 'grid'", $view);
        self::assertStringContainsString('@includeFirst($gridPresentationViews)', $view);
        self::assertStringContainsString("'componentElement' => 'nav'", $view);
        self::assertStringContainsString("'aria-labelledby'", $view);
        self::assertStringContainsString("'aria-label'", $view);
        self::assertStringContainsString("\$format === 'buttons'", $view);
        self::assertStringContainsString("\$format === 'list'", $view);
        self::assertStringContainsString("\$format === 'cards'", $view);
        self::assertStringContainsString("@include('navigation-cards')", $view);
        self::assertStringContainsString('@button([', $view);
        self::assertStringContainsString("'reversePositions' => true", $view);
        self::assertStringContainsString('@link([', $view);
        self::assertStringContainsString('@icon([', $view);
        // The grid renders the resolved item icon (restores LTS grid×menu icons).
        $gridView = file_get_contents(dirname(__DIR__) . '/views/navigation-grid.blade.php');
        self::assertIsString($gridView);
        self::assertStringContainsString('mod-navigation-grid__icon', $gridView);
        self::assertStringContainsString('mod-navigation-grid__title', $gridView);
        self::assertStringContainsString('mod-navigation-grid__description', $gridView);
        self::assertStringContainsString(
            '--mod-navigation-grid-link-padding',
            $styles = file_get_contents(dirname(__DIR__) . '/assets/css/navigation.css'),
        );
        self::assertStringContainsString('--mod-navigation-grid-focus-color', $styles);
        self::assertStringContainsString('.mod-navigation-grid__link:visited', $styles);
        self::assertStringContainsString('.mod-navigation-grid__link:active', $styles);
        self::assertStringContainsString('.mod-navigation-grid__link:visited:active', $styles);
        self::assertStringContainsString('.mod-navigation-grid__link:focus-visible', $styles);
        self::assertStringContainsString('<ul class="mod-navigation-list">', $view);
        self::assertStringContainsString('<li class="mod-navigation-list__item">', $view);
        // Inline and bar "quick links" formats render as accessible lists.
        self::assertStringContainsString("\$format === 'inline'", $view);
        self::assertStringContainsString("\$format === 'bar'", $view);
        self::assertStringContainsString('<ul class="mod-navigation-inline">', $view);
        self::assertStringContainsString('<ul class="mod-navigation-bar">', $view);
        // Navigation icons are decorative — the visible label is the accessible name.
        self::assertStringContainsString("'decorative' => true", $view);
        self::assertStringNotContainsString("\$source ===", $view);
        self::assertStringNotContainsString("@component('mxui.button'", $view);
    }

    public function testCardsUseOnlyModernCardContentWithoutPostMetadata(): void
    {
        $view = file_get_contents(dirname(__DIR__) . '/views/navigation-cards.blade.php');

        self::assertIsString($view);
        self::assertStringContainsString('@card([', $view);
        self::assertStringContainsString("'link' => \$item['href']", $view);
        self::assertStringContainsString("'heading' => \$item['title']", $view);
        self::assertStringContainsString("'content' => \$item['cardExcerpt']", $view);
        self::assertStringContainsString("'image' => \$item['cardImage']", $view);
        self::assertStringContainsString("'hasPlaceholder' => \$item['cardHasPlaceholder']", $view);
        self::assertStringContainsString('u-unlist u-padding--0', $view);
        self::assertStringNotContainsString("'date'", $view);
        self::assertStringNotContainsString("'tags'", $view);
        self::assertStringNotContainsString('readingTime', $view);

        $styles = file_get_contents(dirname(__DIR__) . '/assets/css/navigation.css');
        self::assertIsString($styles);
        self::assertMatchesRegularExpression('/\.mod-navigation-cards\s*\{[^}]*list-style:\s*none;/s', $styles);
    }

    public function testGridUsesThePackageViewAsFallbackWhenAnOverrideIsMissing(): void
    {
        $view = file_get_contents(dirname(__DIR__) . '/views/navigation.blade.php');

        self::assertIsString($view);
        self::assertStringContainsString('@includeFirst($gridPresentationViews)', $view);
        self::assertFileExists(dirname(__DIR__) . '/views/navigation-grid.blade.php');
    }

    public function testListUnderlinesOnlyTheLinkLabel(): void
    {
        $styles = file_get_contents(dirname(__DIR__) . '/assets/css/navigation.css');

        self::assertIsString($styles);
        self::assertMatchesRegularExpression(
            '/\.mod-navigation-list__link\s*\{[^}]*text-decoration:\s*none;/s',
            $styles,
        );
        self::assertMatchesRegularExpression(
            '/\.mod-navigation-list__link:hover,[^{]*\.mod-navigation-list__link:focus,[^{]*\.mod-navigation-list__link:active,[^{]*\.mod-navigation-list__link:visited\s*\{[^}]*text-decoration:\s*none;/s',
            $styles,
        );
        self::assertMatchesRegularExpression(
            '/\.mod-navigation-list__label\s*\{[^}]*text-decoration:\s*underline;/s',
            $styles,
        );
    }
}
