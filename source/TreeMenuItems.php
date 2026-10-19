<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class TreeMenuItems
{
    /**
     * LTS tree metadata differs from flat formats. Read the menu once and take
     * only two levels, without following page parents or malformed menu cycles.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fromMenu(string $slug): array
    {
        $raw = $slug !== '' ? wp_get_nav_menu_items($slug) : false;
        if (!is_array($raw)) {
            return [];
        }
        $levels = [];
        foreach ($raw as $entry) {
            if (!is_object($entry)) {
                continue;
            }
            $item = $this->normalize($entry);
            if ($item !== null) {
                $levels[(int) ($entry->menu_item_parent ?? 0)][] = $item;
            }
        }
        $items = $levels[0] ?? [];
        foreach ($items as &$item) {
            $item['children'] = $levels[$item['menuId']] ?? [];
        }
        return $items;
    }

    /** @return array<string, mixed>|null */
    private function normalize(object $entry): ?array
    {
        $id = (int) ($entry->ID ?? 0);
        if (
            $id <= 0
            || !is_string($entry->title ?? null)
            || $entry->title === ''
            || !is_string($entry->url ?? null)
            || $entry->url === ''
        ) {
            return null;
        }
        // A custom link's object_id is not a connected page in the LTS model.
        $postId = ($entry->type ?? null) === 'post_type' ? (int) ($entry->object_id ?? 0) : 0;
        return [
            'menuId' => $id,
            'postId' => $postId,
            'title' => $entry->title,
            'href' => $entry->url,
            'target' => in_array($entry->target ?? null, ['_self', '_blank', '_parent', '_top'], true)
                ? $entry->target
                : '',
            'description' => $this->description($entry, $postId),
        ];
    }

    private function description(object $entry, int $postId): string
    {
        if (is_string($entry->description ?? null) && $entry->description !== '') {
            return $entry->description;
        }
        $own = get_field('page_navigation_description', (int) $entry->ID);
        // An explicit empty string suppresses the connected page's description in LTS.
        $description = $own ?? ($postId > 0 ? get_field('page_navigation_description', $postId) : null);
        return is_string($description) ? $description : '';
    }
}
