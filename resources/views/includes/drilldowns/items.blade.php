{{-- Если вложенный --}}
<li class="item-catalog">

	<a class="get-products" id="{{ $entity }}-{{ $item->id }}">{{ $item->name }}</a>

	@if(isset($item->childrens))

	<ul class="menu vertical nested">
		@foreach ($item->childrens as $item)
                @include('includes.drilldowns.items', ['item' => $item])
            @endforeach
	</ul>

	@endif

</li>
















