<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Tests;

use MunicipioModularityNavigation\GridPresentation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class GridPresentationTest extends TestCase
{
    public function testItOffersAValidSiteViewBeforeThePackageFallback(): void
    {
        static::assertSame(
            ['nora-navigation-grid', GridPresentation::DEFAULT_VIEW],
            (new GridPresentation())->viewCandidates('nora-navigation-grid'),
        );
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidViews(): iterable
    {
        yield 'missing override' => [null];
        yield 'non-string' => [['unexpected' => 'value']];
        yield 'path traversal' => ['../private-template'];
        yield 'empty string' => [''];
    }

    #[DataProvider('invalidViews')]
    public function testItFallsBackWhenTheOverrideIsMissingOrInvalid(mixed $override): void
    {
        static::assertSame([GridPresentation::DEFAULT_VIEW], (new GridPresentation())->viewCandidates($override));
    }
}
