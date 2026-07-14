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
            if (!is_object($menuItem) || (int) ($menuItem->menu_item_parent ?? 0) !== 0) {
                continue;
            }

            $title = is_string($menuItem->title ?? null) ? $menuItem->title : '';
            $href = is_string($menuItem->url ?? null) ? $menuItem->url : '';

            if ($title === '' || $href === '') {
                continue;
            }

            $items[] = [
                'title' => $title,
                'href' => $href,
                'description' => $this->getDescription($menuItem),
                'target' => in_array($menuItem->target ?? null, ['_self', '_blank', '_parent', '_top'], true)
                    ? $menuItem->target
                    : '',
                'icon' => '',
                'buttonVariant' => 'default',
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
        $description = is_string($menuItem->description ?? null) ? $menuItem->description : '';

        if ($description !== '' || !function_exists('get_field')) {
            return $description;
        }

        $connectedPostId = (int) ($menuItem->object_id ?? 0);

        $fallback = $connectedPostId > 0 ? get_field('page_navigation_description', $connectedPostId) : '';

        return is_string($fallback) ? $fallback : '';
    }
}
