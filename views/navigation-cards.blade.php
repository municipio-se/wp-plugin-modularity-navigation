<ul class="u-unlist u-padding--0 mod-navigation-cards">
    @foreach ($items as $item)
        <li class="mod-navigation-cards__item">
            @card([
                'context' => ['module.navigation.cards'],
                'link' => $item['href'],
                'heading' => $item['title'],
                'content' => $item['cardExcerpt'] !== '' ? $item['cardExcerpt'] : false,
                'image' => $item['cardImage'],
                'hasPlaceholder' => $item['cardHasPlaceholder'],
                'classList' => ['u-height--100'],
                'containerAware' => true,
                'attributeList' => array_filter([
                    'target' => $item['target'] !== '' ? $item['target'] : null,
                    'rel' => $item['target'] === '_blank' ? 'noopener noreferrer' : null,
                ]),
            ])
            @endcard
        </li>
    @endforeach
</ul>
