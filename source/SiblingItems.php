<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class SiblingItems
{
    /**
     * Return the current post's visible siblings in the ordering used by Municipio LTS.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fromPost(int $postId): array
    {
        $currentPost = $postId > 0 ? get_post($postId) : null;

        if (!$currentPost instanceof \WP_Post) {
            return [];
        }

        $siblings = get_posts([
            'post_parent' => $currentPost->post_parent,
            'post_type' => $currentPost->post_type,
            'post__not_in' => [$currentPost->ID],
            'nopaging' => true,
            'post_status' => 'publish',
            'orderby' => 'menu_order',
            'order' => 'DESC',
            'update_post_meta_cache' => true,
        ]);

        if (!is_array($siblings)) {
            return [];
        }

        $items = [];

        foreach ($siblings as $sibling) {
            if (!$sibling instanceof \WP_Post) {
                continue;
            }

            // get_posts primes metadata in bulk, avoiding joins for each sibling's visibility.
            // Match the previous OR query: missing metadata or any non-hidden value is visible.
            $visibility = get_post_meta($sibling->ID, 'hide_in_menu', false);
            if ($visibility !== [] && array_filter($visibility, static fn($value) => $value !== '1') === []) {
                continue;
            }

            $href = get_permalink($sibling->ID);
            $title = $this->postField('custom_menu_title', $sibling);

            if ($title === '') {
                $title = is_string($sibling->post_title) ? $sibling->post_title : '';
            }

            if (!is_string($href) || $href === '' || $title === '') {
                continue;
            }

            $icon = function_exists('get_field') ? get_field('page_navigation_icon', $sibling->ID) : null;

            $items[] = [
                'title' => $title,
                'href' => $href,
                'description' => $this->postField('page_navigation_description', $sibling),
                'target' => '',
                'icon' => $this->iconName($icon),
                'buttonVariant' => 'default',
                'postId' => $sibling->ID,
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
