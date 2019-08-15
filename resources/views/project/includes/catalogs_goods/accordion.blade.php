		@isset($catalogs_goods_items)
			<ul class="vertical menu accordion-menu" data-multi-open="false" data-accordion-menu data-submenu-toggle="false">
				@foreach ($catalogs_goods_items as $catalogs_goods_item)
					@if(is_null($catalogs_goods_item->parent_id))
						<li><a href="#"><h3>{{ $catalogs_goods_item->name }}</h3></a>
							@isset ($catalogs_goods_item->childrens)
								@include($site->alias.'.includes.catalogs_goods.sidebar_item', ['items' => $catalogs_goods_item->childrens])
							@endisset
						</li>
					@endif
				@endforeach
			</ul>
		@endisset