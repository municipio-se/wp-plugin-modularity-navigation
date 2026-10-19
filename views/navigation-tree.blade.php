<ul class="mod-navigation-tree mod-navigation-tree--{{ $treeStyle }}" role="list">
    @foreach ($items as $item)
        <li class="mod-navigation-tree__item{{ $item['treeImage'] !== '' ? ' mod-navigation-tree__item--with-image' : '' }}">
            <div class="mod-navigation-tree__main">
                @if ($item['treeImage'] !== '')
                    <div class="mod-navigation-tree__media">{!! $item['treeImage'] !!}</div>
                @endif
                <div class="mod-navigation-tree__text">
                    @typography([
                        'element' => 'h3',
                        'autopromote' => true,
                        'classList' => ['mod-navigation-tree__heading'],
                    ])
                        @link([
                            'href' => $item['href'],
                            'target' => $item['target'] !== '' ? $item['target'] : '_self',
                            'xfn' => $item['target'] === '_blank' ? 'noopener noreferrer' : false,
                            'classList' => ['mod-navigation-tree__link'],
                        ])
                            {{ $item['title'] }}
                        @endlink
                    @endtypography
                    @if ($item['description'] !== '')
                        <p class="mod-navigation-tree__description">{{ $item['description'] }}</p>
                    @endif
                </div>
            </div>
            @if (!empty($item['children']))
                <ul class="mod-navigation-tree__children" role="list">
                    @foreach ($item['children'] as $child)
                        <li>
                            @link([
                                'href' => $child['href'],
                                'target' => $child['target'] !== '' ? $child['target'] : '_self',
                                'xfn' => $child['target'] === '_blank' ? 'noopener noreferrer' : false,
                                'classList' => ['mod-navigation-tree__child-link'],
                            ])
                                {{ $child['title'] }}
                            @endlink
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
