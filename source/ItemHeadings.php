<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

/**
 * Rendering first preserves Typography's existing context-dependent variants.
 * Change only our item title tags, preserving source bytes, entities and SVGs.
 */
final class ItemHeadings
{
    public static function transform(string $html, string $element, string $format = 'cards'): string
    {
        if (!in_array($element, ['h2', 'h3'], true) || !class_exists(\WP_HTML_Tag_Processor::class)) {
            return $html;
        }

        if (!in_array($format, ['cards', 'tree'], true)) {
            return $html;
        }
        return (new HeadingTags($html))->replace($element[1], $format);
    }
}
