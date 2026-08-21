<ul class="mod-navigation-grid mod-navigation-grid--{{ esc_attr($gridStyle) }}">
    @foreach ($items as $item)
        <li class="mod-navigation-grid__item">
            <a
                class="mod-navigation-grid__link"
                href="{{ esc_url($item['href']) }}"
                @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif
            >
                @if ($item['icon'] !== '')
                    {{-- Decorative: the adjacent title is the accessible name, so the icon
                         must not be announced (renders aria-hidden, empty aria-label). --}}
                    @icon([
                        'icon' => $item['icon'],
                        'size' => 'lg',
                        'decorative' => true,
                        'classList' => ['mod-navigation-grid__icon'],
                    ])
                    @endicon
                @endif
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
