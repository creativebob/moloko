
		@isset($catalog_goods)
			<ul class="cell menu vertical medium-horizontal catalogs-goods-menu">
				@foreach($catalog_goods->items as $item)
					@if($item->slug == $page->alias)
						<li class="is-active"><span class="isactive-item" >{{ $item->name }}</span></li>
					@else
						<li><a href="/{{ $item->slug }}">{{ $item->name }}</a></li>
					@endif
				@endforeach
			</ul>
		@endisset