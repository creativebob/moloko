{{-- Если вложенный --}}
@foreach ($items as $item)
<li>
	<a href="#">{{ $item->name }}</a>

	@if ($item->childs->isNotEmpty())
	<ul class="menu vertical nested">
		@include('leads.items_drilldown', ['items' => $item->childs])
	</ul>
	@endif

</li>
@endforeach
















