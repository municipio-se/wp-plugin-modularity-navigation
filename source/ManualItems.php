<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class ManualItems
{
    /**
     * Keep the released LTS repeater shape as the read contract. Invalid rows are skipped so a
     * partially malformed import cannot produce warnings or empty links.
     *
     * @return array<int, array<string, string>>
     */
    public function fromFields(mixed $rawItems): array
    {
        if (!is_array($rawItems)) {
            return [];
        }

        $items = [];

        foreach ($rawItems as $rawItem) {
            if (!is_array($rawItem) || !is_array($rawItem['link'] ?? null)) {
                continue;
            }

            $link = $rawItem['link'];
            $href = $this->stringValue($link['url'] ?? null);

            if ($href === '') {
                continue;
            }

            $post = $this->linkedPost($href);
            $title = $this->stringValue($link['title'] ?? null);

            if ($title === '' && $post instanceof \WP_Post) {
                $title = $this->postTitle($post);
            }

            if ($title === '') {
                continue;
            }

            $items[] = [
                'title' => $title,
                'href' => $href,
                'description' => $this->postField('page_navigation_description', $post),
                'target' => $this->linkTarget($link['target'] ?? null),
                'icon' => $this->iconName($rawItem['icon'] ?? null, $post),
                'buttonVariant' => $this->buttonVariant($rawItem['button_variant'] ?? null),
            ];
        }

        return $items;
    }

    private function linkedPost(string $href): ?\WP_Post
    {
        if (!function_exists('url_to_postid') || !function_exists('get_post')) {
            return null;
        }

        $postId = url_to_postid($href);
        $post = $postId > 0 ? get_post($postId) : null;

        return $post instanceof \WP_Post ? $post : null;
    }

    private function postTitle(\WP_Post $post): string
    {
        $customTitle = $this->postField('custom_menu_title', $post);

        return $customTitle !== '' ? $customTitle : $this->stringValue($post->post_title ?? null);
    }

    private function postField(string $fieldName, ?\WP_Post $post): string
    {
        if (!$post instanceof \WP_Post || !function_exists('get_field')) {
            return '';
        }

        return $this->stringValue(get_field($fieldName, $post->ID));
    }

    private function iconName(mixed $value, ?\WP_Post $post): string
    {
        if (is_array($value)) {
            $value = $value['name'] ?? $value['icon'] ?? $value['material_icon'] ?? null;
        }

        $icon = $this->stringValue($value);

        return $icon !== '' ? $icon : $this->postField('page_navigation_icon', $post);
    }

    private function buttonVariant(mixed $value): string
    {
        return in_array($value, ['primary', 'secondary', 'default'], true) ? $value : 'default';
    }

    private function linkTarget(mixed $value): string
    {
        return in_array($value, ['_self', '_blank', '_parent', '_top'], true) ? $value : '';
    }

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
