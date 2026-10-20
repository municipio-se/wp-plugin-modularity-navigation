<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class HeadingTags extends \WP_HTML_Tag_Processor
{
    public function replace(string $level, string $format): string
    {
        $open = null;
        $replacements = [];
        $cardDepth = 0;
        $stack = [];
        $rootClass = $format === 'cards' ? 'c-card' : 'mod-navigation-tree';
        $headingClass = $format === 'cards' ? 'c-card__heading' : 'mod-navigation-tree__heading';
        $void = [
            'AREA',
            'BASE',
            'BR',
            'COL',
            'EMBED',
            'HR',
            'IMG',
            'INPUT',
            'LINK',
            'META',
            'PARAM',
            'SOURCE',
            'TRACK',
            'WBR',
        ];

        while ($this->next_tag(['tag_closers' => 'visit'])) {
            $tag = $this->get_tag();
            if ($this->is_tag_closer()) {
                $entry = array_pop($stack);
                if ($entry === null || $entry['tag'] !== $tag) {
                    return $this->html;
                }
                if ($entry['card']) {
                    --$cardDepth;
                }
            } else {
                $entry = [
                    'tag' => $tag,
                    'card' => $this->has_class($rootClass),
                    'content' => $this->has_class('c-card__content') || $this->has_class('c-card__body-content'),
                ];
                if ($entry['card']) {
                    ++$cardDepth;
                }
                if (!in_array($tag, $void, true) && !$this->has_self_closing_flag()) {
                    $stack[] = $entry;
                }
            }

            if (!preg_match('/^H[1-6]$/D', $tag ?? '')) {
                continue;
            }
            if (!$this->is_tag_closer()) {
                if ($open !== null) {
                    return $this->html;
                }
                $open = [
                    'tag' => $tag,
                    'target' => $cardDepth === 1 && $this->has_class($headingClass) && !$this->insideContent($stack),
                ];
            } elseif ($open === null || $open['tag'] !== $tag) {
                return $this->html;
            }
            if ($open['target']) {
                $this->set_bookmark('navigation-heading');
                $replacements[] = $this->bookmarks['navigation-heading']->start + ($this->is_tag_closer() ? 3 : 2);
                $this->release_bookmark('navigation-heading');
            }
            if ($this->is_tag_closer()) {
                $open = null;
            }
        }
        if ($open !== null || $stack !== []) {
            return $this->html;
        }
        $html = $this->html;
        foreach ($replacements as $offset) {
            $html[$offset] = $level;
        }
        return $html;
    }

    private function insideContent(array $stack): bool
    {
        return in_array(true, array_column($stack, 'content'), true);
    }
}
