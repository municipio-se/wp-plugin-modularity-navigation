<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class TreeItems
{
    /**
     * Only the children source expands page parents. Manual and sibling selections
     * stay flat, even when a selected page has children of its own.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    public function fromItems(array $items, string $source): array
    {
        foreach ($items as &$item) {
            $postId = (int) ($item['postId'] ?? 0);
            if ($source === 'children') {
                $item['children'] = (new ChildrenItems())->fromPost($postId);
            }
            $item['children'] ??= [];
            $menuId = (int) ($item['menuId'] ?? 0);
            $imageId = $menuId > 0 ? (int) get_post_thumbnail_id($menuId) : 0;
            if ($imageId <= 0 && $postId > 0) {
                $imageId = (int) get_post_thumbnail_id($postId);
            }
            // WordPress owns responsive sources and image filters. The adjacent heading
            // names this navigational image; it is neither a second link nor extra link text.
            $item['treeImage'] = $imageId > 0 ? wp_get_attachment_image($imageId, 'medium', false, [
                    'alt' => '',
                    'class' => 'mod-navigation-tree__image',
                    'loading' => 'lazy',
                ]) : '';
        }
        return $items;
    }
}
