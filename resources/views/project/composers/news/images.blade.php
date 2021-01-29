		@isset($news_list)
			@if($news_list->count() > 0)
				<div class="grid-x grid-padding-x">
					<div class="cell small-12 wrap-news">
						<h3>Новости</h3>
						<ul class="grid-x grid-padding-x news-list">
							@foreach($news_list as $item)

								<li class="cell news-item text-center">
									<img src="{{ getPhotoPath($item, 'medium') }}" alt="{{ $item->alt }}" width="440" height="292">
									{!! $item->content !!}
									<span class="publish_date">{{ $item->publish_begin_date->isoFormat('MMMM YYYY') }}</span>
								</li>

							@endforeach
						</ul>
					</div>
				</div>
			@endif
		@endisset