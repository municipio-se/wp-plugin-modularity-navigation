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
        self::assertStringContainsString("\$format === 'buttons'", $view);
        self::assertStringContainsString("\$format === 'list'", $view);
        self::assertStringContainsString('@button([', $view);
        self::assertStringContainsString("'reversePositions' => true", $view);
        self::assertStringContainsString('@link([', $view);
        self::assertStringContainsString('@icon([', $view);
        // The grid renders the resolved item icon (restores LTS grid×menu icons).
        self::assertStringContainsString('mod-navigation-grid__icon', $view);
        self::assertStringContainsString('<ul class="mod-navigation-list">', $view);
        self::assertStringContainsString('<li class="mod-navigation-list__item">', $view);
        self::assertStringNotContainsString("\$source ===", $view);
        self::assertStringNotContainsString("@component('mxui.button'", $view);
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
