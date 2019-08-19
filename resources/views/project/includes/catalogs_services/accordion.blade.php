		@isset($catalogs_services_items)
			<ul class="vertical menu accordion-menu" data-multi-open="false" data-accordion-menu data-submenu-toggle="false">
				@foreach ($catalogs_services_items as $catalogs_services_item)
					@if(is_null($catalogs_services_item->parent_id))
						<li><a href="/catalogs-services/{{ $catalogs_services_item->catalog->slug }}/{{ $catalogs_services_item->slug }}"><h3>{{ $catalogs_services_item->name }}</h3></a>
							@isset ($catalogs_services_item->childrens)
								@include('project.includes.catalogs_goods.sidebar_item', ['items' => $catalogs_services_item->childrens])
							@endisset
						</li>
					@endif
				@endforeach
			</ul>
		@endisset