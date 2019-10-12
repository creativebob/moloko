		@isset($catalog_goods)
			<ul class="grid-x grid-padding-x images-menu-list small-up-2 medium-up-3 large-up-5 align-center">
				@foreach($catalog_goods->items as $item)
					@if($item->slug == $page->alias)
						<li class="cell images-menu-item is-active" data-equalizer-watch>
							<span class="isactive-item">
								<div class="wrap-photo">
									<img src="{{ getPhotoPath($item, 'medium') }}" alt="" title="">
								</div>
							</span>
						</li>
					@else
						<li class="cell images-menu-item" data-equalizer-watch>
							<a href="/catalogs-goods/{{ $catalog_goods->slug . '/' . $item->slug }}">
								<div class="wrap-photo align-center">
									<img src="{{ getPhotoPath($item, 'medium') }}" alt="" title="">
								</div>
								<span>{{ $item->name }}</span>
							</a>
							
						</li>
					@endif
				@endforeach
			</ul>
		@endisset