{{-- Если вложенный --}}
@foreach ($catalogs_items as $catalogs_item)
<li class="item-catalog">

	<a class="get-prices" id="{{ $catalogs_item->getTable() }}-{{ $catalogs_item->id }}">{{ $catalogs_item->name }}</a>

	@if ($catalogs_item->childs->isNotEmpty())

	<ul class="menu vertical nested">
        @include('prices_services.sync.catalogs_items_childs', ['catalogs_items' => $catalogs_item->childs])
	</ul>

	@endif

</li>
@endforeach
















