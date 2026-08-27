<ul class="o-grid o-grid--stretch u-unlist u-padding--0 mod-navigation-cards">
    @foreach ($items as $item)
        <li class="o-grid-12@sm o-grid-6@md o-grid-4@lg mod-navigation-cards__item">
            @card([
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
