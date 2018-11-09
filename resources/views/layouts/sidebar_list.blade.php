@if (isset($item->childrens))
<li><a data-link="{{ $item->id }}"><span>{{ $item->name }}</span></a>
	@if (isset($item->children))
	<ul class="menu vertical nested">
		@foreach($item->childrens as $children)
		@include('layouts.sidebar_list', ['item' => $children])
		@endforeach
	</ul>
	@endif
</li>

@else
{{-- Если конечный пункт --}}
<li>
	<a href="/{{ $item->alias }}" data-link="{{ $item->id }}">{{ $item->name }}</a>
</li>
@endif












