@foreach($items as $item)
<li
    data-level="{{ $item->level }}"
    @isset($catalogs_goods_item)
        @if($catalogs_goods_item->slug == $item->slug)
            class="active"
        @endif
    @endisset
>
	@isset ($item->childrens)
        <a>{{ $item->name }}</a>
        <ul class="menu vertical nested">
		@include('viandiesel.composers.catalogs_goods.sidebar_item', ['items' => $item->childrens])
        </ul>
    @else
        <a href="/catalogs-goods/{{ $catalog_goods->slug }}/{{ $item->slug }}">{{ $item->name }}</a>
	@endisset
</li>
@endforeach

