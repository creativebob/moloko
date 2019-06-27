{{-- Если вложенный --}}
@foreach ($items as $item)
<li class="item-catalog">

	<a class="get-prices" id="{{ $item->getTable() }}-{{ $item->id }}">{{ $item->name }}</a>

	@if ($item->childs->isNotEmpty())

	<ul class="menu vertical nested">
        @include('leads.catalogs.catalogs_items_childs', ['items' => $item->childs])
	</ul>

	@endif

</li>
@endforeach
















