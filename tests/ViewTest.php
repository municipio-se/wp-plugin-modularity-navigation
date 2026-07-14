<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    public function testItUsesCurrentMunicipioPresentationForBothFormats(): void
    {
        $view = file_get_contents(dirname(__DIR__) . '/views/navigation.blade.php');

        self::assertIsString($view);
        self::assertStringContainsString("\$format === 'grid'", $view);
        self::assertStringContainsString("\$format === 'buttons'", $view);
        self::assertStringContainsString('@button([', $view);
        self::assertStringContainsString("'reversePositions' => true", $view);
        self::assertStringNotContainsString("\$source ===", $view);
        self::assertStringNotContainsString("@component('mxui.button'", $view);
    }
}
