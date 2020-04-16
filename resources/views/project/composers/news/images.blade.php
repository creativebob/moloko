		@isset($news_list)
			<ul class="grid-x grid-padding-x news-list">
				@foreach($news_list as $item)

					<li class="cell news-item text-center">
                        <img src="{{ getPhotoPath($item, 'original') }}" alt="">
						{!! $item->content !!}
					</li>

				@endforeach
			</ul>
		@endisset