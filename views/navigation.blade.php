@if (!empty($items) || ($showIfEmpty && $emptyMessage !== ''))
    @if (!$hideTitle && !empty($postTitle))
        @typography([
            'id' => 'mod-navigation-' . $ID . '-label',
            'element' => 'h2',
            'autopromote' => true,
            'classList' => ['module-title'],
        ])
            {{ $postTitle }}
        @endtypography
    @endif

    @if (!empty($items) && $format === 'grid')
        <ul class="mod-navigation-grid mod-navigation-grid--{{ esc_attr($gridStyle) }}">
            @foreach ($items as $item)
                <li class="mod-navigation-grid__item">
                    <a
                        class="mod-navigation-grid__link"
                        href="{{ esc_url($item['href']) }}"
                        @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif
                    >
                        <span class="mod-navigation-grid__title">{{ $item['title'] }}</span>
                        @if ($item['description'] !== '')
                            @typography([
                                'element' => 'span',
                                'variant' => 'meta',
                                'classList' => ['mod-navigation-grid__description'],
                            ])
                                {{ $item['description'] }}
                            @endtypography
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    @elseif (!empty($items) && $format === 'buttons')
        <ul class="mod-navigation-buttons">
            @foreach ($items as $item)
                <li class="mod-navigation-buttons__item">
                    @button([
                        'text' => $item['title'],
                        'href' => $item['href'],
                        'target' => $item['target'] !== '' ? $item['target'] : '_self',
                        'color' => $item['buttonVariant'],
                        'style' => 'filled',
                        'icon' => $item['icon'] !== '' ? $item['icon'] : false,
                        'reversePositions' => true,
                        'disableColor' => false,
                        'attributeList' => $item['target'] === '_blank'
                            ? ['rel' => 'noopener noreferrer']
                            : [],
                    ])
                    @endbutton
                </li>
            @endforeach
        </ul>
    @elseif (!empty($items) && $format === 'list')
        <ul class="mod-navigation-list">
            @foreach ($items as $item)
                <li class="mod-navigation-list__item">
                    @link([
                        'href' => $item['href'],
                        'target' => $item['target'] !== '' ? $item['target'] : '_self',
                        'xfn' => $item['target'] === '_blank' ? 'noopener noreferrer' : false,
                        'classList' => ['mod-navigation-list__link'],
                    ])
                        @icon([
                            'icon' => $item['icon'] !== '' ? $item['icon'] : 'arrow_forward',
                            'size' => 'md',
                            'classList' => ['mod-navigation-list__icon'],
                        ])
                        @endicon
                        <span class="mod-navigation-list__label">{{ $item['title'] }}</span>
                    @endlink
                </li>
            @endforeach
        </ul>
    @elseif ($showIfEmpty && $emptyMessage !== '')
        <div class="mod-navigation-grid__empty-message">
            {!! wp_kses_post($emptyMessage) !!}
        </div>
    @endif
@endif
