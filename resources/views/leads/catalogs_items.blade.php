<ul class="vertical menu drilldown" data-drilldown data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'>

    @foreach ($catalog->items as $item)
    @if(is_null($item->parent_id))

    {{-- Если категория --}}
    <li class="item-catalog">
        <a class="get-prices" id="{{ $item->getTable() }}-{{ $item->id }}">{{ $item->name }}</a>

        @if($item->childs->isNotEmpty())

        <ul class="menu vertical nested">

            @include('leads.catalogs_items_childs', ['items' => $item->childs])

        </ul>

        @endif

    </li>

    @endif
    @endforeach

</ul>