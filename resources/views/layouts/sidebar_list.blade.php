@if (isset($item->childrens))

<li>
	<a data-link="{{ $item->id }}"><span>{{ $item->name }}</span></a>
	<ul class="menu vertical nested">
		@foreach($item->childrens as $children)
		@include('layouts.sidebar_list', ['item' => $children])
		@endforeach
	</ul>
</li>

@else

{{-- Если конечный пункт --}}
@isset($item->alias)

<li>
	<a href="/{{ $item->alias }}" data-link="{{ $item->id }}">{{ $item->name }}</a>
</li>
@endisset

@endif












