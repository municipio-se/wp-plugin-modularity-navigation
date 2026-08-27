<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class ChildrenItems
{
    /**
     * Reproduce the released LTS child-page query while normalizing its result to the same
     * flat link model used by every supported presentation. Navigation's supported formats
     * intentionally render only the first child level, so no depth migration is required.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fromPost(int $postId): array
    {
        $parent = $postId > 0 ? get_post($postId) : null;

        if (!$parent instanceof \WP_Post) {
            return [];
        }

        $children = get_posts([
            'post_parent' => $parent->ID,
            'post_type' => $parent->post_type,
            'nopaging' => true,
            'post_status' => 'publish',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'hide_in_menu',
                    'value' => '1',
                    'compare' => '!=',
                ],
                [
                    'key' => 'hide_in_menu',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ]);

        if (!is_array($children)) {
            return [];
        }

        $items = [];

        foreach ($children as $child) {
            if (!$child instanceof \WP_Post) {
                continue;
            }

            $href = get_permalink($child->ID);
            $title = $this->postField('custom_menu_title', $child);

            if ($title === '') {
                $title = is_string($child->post_title) ? $child->post_title : '';
            }

            if (!is_string($href) || $href === '' || $title === '') {
                continue;
            }

            $icon = function_exists('get_field') ? get_field('page_navigation_icon', $child->ID) : null;

            $items[] = [
                'title' => $title,
                'href' => $href,
                'description' => $this->postField('page_navigation_description', $child),
                'target' => '',
                'icon' => $this->iconName($icon),
                'buttonVariant' => 'default',
                'postId' => $child->ID,
            ];
        }

        return $items;
    }

    private function postField(string $fieldName, \WP_Post $post): string
    {
        if (!function_exists('get_field')) {
            return '';
        }

        $value = get_field($fieldName, $post->ID);

        return is_string($value) ? $value : '';
    }

    private function iconName(mixed $value): string
    {
        if (is_array($value)) {
            $value = $value['name'] ?? $value['icon'] ?? $value['material_icon'] ?? null;
        }

        return is_string($value) ? $value : '';
    }
}
