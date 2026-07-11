@if (!empty($items) || $showIfEmpty)
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

    @if (!empty($items) && $format === 'grid' && $source === 'menu')
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
                            <span class="mod-navigation-grid__description">{{ $item['description'] }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    @elseif ($showIfEmpty && $emptyMessage !== '')
        <div class="mod-navigation-grid__empty-message">
            {!! wp_kses_post($emptyMessage) !!}
        </div>
    @endif
@endif
