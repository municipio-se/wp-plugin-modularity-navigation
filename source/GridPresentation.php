<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class GridPresentation
{
    public const DEFAULT_VIEW = 'navigation-grid';

    /**
     * Builds an include-first list with the package-owned presentation as a guaranteed fallback.
     * Restricting overrides to a plain Blade view name prevents path traversal while still letting
     * a site plugin register the view through Modularitys existing external-view-path filter.
     *
     * @return array<int, string>
     */
    public function viewCandidates(mixed $override): array
    {
        if (!$this->isValidViewName($override) || $override === self::DEFAULT_VIEW) {
            return [self::DEFAULT_VIEW];
        }

        return [$override, self::DEFAULT_VIEW];
    }

    private function isValidViewName(mixed $view): bool
    {
        return is_string($view) && preg_match('/^[a-z0-9][a-z0-9._-]*$/i', $view) === 1;
    }
}
