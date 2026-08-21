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
        @includeFirst($gridPresentationViews)
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
                        {{-- Decorative: the label span carries the accessible name. --}}
                        @icon([
                            'icon' => $item['icon'] !== '' ? $item['icon'] : 'arrow_forward',
                            'size' => 'md',
                            'decorative' => true,
                            'classList' => ['mod-navigation-list__icon'],
                        ])
                        @endicon
                        <span class="mod-navigation-list__label">{{ $item['title'] }}</span>
                    @endlink
                </li>
            @endforeach
        </ul>
    @elseif (!empty($items) && $format === 'inline')
        {{-- Inline and bar are distinct LTS "quick links" presentations on a primary-colored band
             (mxui.navigation.inline / .bar). Inline is a horizontal, wrapping row of icon-beside-label
             chips; bar is an auto-fit grid of stacked icon-above-label columns. They share the band,
             contrasting text and underlined label, so they differ in CSS layout only — the item model
             and markup contract are identical. Icons are decorative (label is the accessible name) and
             fall back to arrow_forward for menu items without an icon, matching the LTS render. --}}
        <ul class="mod-navigation-inline">
            @foreach ($items as $item)
                <li class="mod-navigation-inline__item">
                    @link([
                        'href' => $item['href'],
                        'target' => $item['target'] !== '' ? $item['target'] : '_self',
                        'xfn' => $item['target'] === '_blank' ? 'noopener noreferrer' : false,
                        'classList' => ['mod-navigation-inline__link'],
                    ])
                        @icon([
                            'icon' => $item['icon'] !== '' ? $item['icon'] : 'arrow_forward',
                            'size' => 'md',
                            'decorative' => true,
                            'classList' => ['mod-navigation-inline__icon'],
                        ])
                        @endicon
                        <span class="mod-navigation-inline__label">{{ $item['title'] }}</span>
                    @endlink
                </li>
            @endforeach
        </ul>
    @elseif (!empty($items) && $format === 'bar')
        <ul class="mod-navigation-bar">
            @foreach ($items as $item)
                <li class="mod-navigation-bar__item">
                    @link([
                        'href' => $item['href'],
                        'target' => $item['target'] !== '' ? $item['target'] : '_self',
                        'xfn' => $item['target'] === '_blank' ? 'noopener noreferrer' : false,
                        'classList' => ['mod-navigation-bar__link'],
                    ])
                        @icon([
                            'icon' => $item['icon'] !== '' ? $item['icon'] : 'arrow_forward',
                            'size' => 'lg',
                            'decorative' => true,
                            'classList' => ['mod-navigation-bar__icon'],
                        ])
                        @endicon
                        <span class="mod-navigation-bar__label">{{ $item['title'] }}</span>
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
