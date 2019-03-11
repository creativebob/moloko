{{-- <li @if ($current_category->id == $item['id']) class=active @endif> --}}
	@foreach ($catalogs_items as $catalogs_item)
	<li>
		@if (isset($catalogs_item->children))
		<a>{{ $catalogs_item->name}}</a>
		<ul class="menu vertical nested ">
			{{-- @if (isset($item['item_id'])) is-active @endif --}}
			@foreach ($catalogs_item->childrens as $item)
			@include('project.includes.catalog.catalogs_item', ['catalogs_item' => $item])
			@endforeach
		</ul>
		@else
		<a href="/goods/{{ $catalogs_item->id }}">{{ $catalogs_item->name }}</a>
		@endif
	</li>
	@endforeach
