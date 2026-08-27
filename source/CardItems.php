<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class CardItems
{
    /**
     * Decorate normalized navigation links with the same post presentation data
     * that Municipios Posts module supplies to its Card component. Navigation's
     * title, URL and target remain authoritative; the local post contributes
     * only image and excerpt data.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    public function fromItems(array $items): array
    {
        foreach ($items as &$item) {
            $post = $this->resolvePost($item);
            $postObject = $this->preparePost($post);
            $image = $postObject !== null ? $postObject->getImage() : null;

            $item['cardExcerpt'] = $postObject !== null ? (string) ($postObject->excerptShort ?? '') : '';
            $item['cardImage'] = $image;
            $item['cardHasPlaceholder'] = $image === null;
        }

        return $items;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function resolvePost(array $item): ?\WP_Post
    {
        $postId = (int) ($item['postId'] ?? 0);

        if ($postId <= 0) {
            $href = (string) ($item['href'] ?? '');
            $postId = $href !== '' ? (int) url_to_postid($href) : 0;
        }

        $post = $postId > 0 ? get_post($postId) : null;

        return $post instanceof \WP_Post ? $post : null;
    }

    private function preparePost(?\WP_Post $post): ?object
    {
        if (!$post instanceof \WP_Post || !class_exists(\Municipio\Helper\Post::class)) {
            return null;
        }

        return \Municipio\Helper\Post::preparePostObjectArchive($post);
    }
}
