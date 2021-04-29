@isset($catalog_goods)
    <aside class="cell small-12 medium-5 large-3 sidebar" data-sticky-container>
        <div class="sticky" data-sticky data-sticky-on="medium" data-top-anchor="278" data-btm-anchor="wrap-sidebar:bottom" data-margin-top="2">
            <h3>{{ $catalog_goods->name }}:</h3>
            @if($catalog_goods->items->isNotEmpty())
                <ul class="vertical menu accordion-menu" id="sidebar-catalog_goods" data-multi-open="false" data-accordion-menu>
                @foreach ($catalog_goods->items as $item)
                        <li
                            data-level="{{ $item->level }}"
                            @isset($catalogs_goods_item)
                                @if($catalogs_goods_item->slug == $item->slug)
                                    class="active"
                                @endif
                            @endisset
                            >
                            <a href="/catalogs-goods/{{ $catalog_goods->slug }}/{{ $item->slug }}">{{ $item->name }}</a>
                        </li>
                @endforeach
                </ul>
            @endif
        </div>
    </aside>
@endisset
