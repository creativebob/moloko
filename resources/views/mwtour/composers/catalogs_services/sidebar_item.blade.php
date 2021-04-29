@foreach($items as $item)
<li
    data-level="{{ $item->level }}"
    @isset($catalogs_services_item)
        @if($catalogs_services_item->slug == $item->slug)
            class="active"
        @endif
    @endisset
>
	@isset ($item->childrens)
        <a>{{ $item->name }}</a>
        <ul class="menu vertical nested">
		@include('viandiesel.composers.catalogs_services.sidebar_item', ['items' => $item->childrens])
        </ul>
    @else
        <a href="/catalogs-services/{{ $catalog_services->slug }}/{{ $item->slug }}">{{ $item->name }}</a>
	@endisset
</li>
@endforeach

