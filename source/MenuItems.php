<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class MenuItems
{
    /**
     * Keep only top-level items, matching the LTS grid behavior. Nested menu items belong
     * to the formats deliberately left outside this first port.
     *
     * @return array<int, array<string, string>>
     */
    public function fromMenu(string $menuSlug): array
    {
        if ($menuSlug === '') {
            return [];
        }

        $menuItems = wp_get_nav_menu_items($menuSlug);

        if (!is_array($menuItems)) {
            return [];
        }

        $items = [];

        foreach ($menuItems as $menuItem) {
            if ((int) ($menuItem->menu_item_parent ?? 0) !== 0) {
                continue;
            }

            $items[] = [
                'title' => (string) ($menuItem->title ?? ''),
                'href' => (string) ($menuItem->url ?? ''),
                'description' => $this->getDescription($menuItem),
                'target' => (string) ($menuItem->target ?? ''),
            ];
        }

        return $items;
    }

    /**
     * LTS allowed menu-item descriptions to fall back to the connected page's navigation
     * description. Höör relies on that fallback for the text below every grid heading.
     */
    private function getDescription(object $menuItem): string
    {
        $description = (string) ($menuItem->description ?? '');

        if ($description !== '' || !function_exists('get_field')) {
            return $description;
        }

        $connectedPostId = (int) ($menuItem->object_id ?? 0);

        return $connectedPostId > 0 ? (string) get_field('page_navigation_description', $connectedPostId) : '';
    }
}
