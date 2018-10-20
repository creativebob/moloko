{{-- Если вложенный --}}

@foreach ($items as $item)
<li>
	<a class="get-products" id="{{ $entity }}-{{ $item->id }}">{{ $item->name }}</a>
	@if(isset($grouped_items[$item->id]))

	<ul class="menu vertical nested">
		@include('includes.drilldown_views.items_drilldown', ['items' => $grouped_items[$item->id], 'entity' => $entity])
	</ul>

	@endif
</li>
@endforeach
















