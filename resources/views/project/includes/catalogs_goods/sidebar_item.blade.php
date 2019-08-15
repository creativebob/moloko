<ul class="menu vertical nested">
	@foreach($items as $item)
		<li>
			<a href="#">{{ $item->name }}</a>
			@isset ($item->childrens)
				@include('project.includes.sidebar_item', ['items' => $item->childrens])
			@endisset
		</li>
	@endforeach
</ul>