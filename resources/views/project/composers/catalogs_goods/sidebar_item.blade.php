<ul class="menu vertical nested">
	@foreach($items as $item)
		<li>
			<a href="/catalogs-goods/{{ $item->catalog->slug }}/{{ $item->slug }}">{{ $item->name }}</a>
			@isset ($item->childrens)
				@include('project.composers.sidebar_item', ['items' => $item->childrens])
			@endisset
		</li>
	@endforeach
</ul>
