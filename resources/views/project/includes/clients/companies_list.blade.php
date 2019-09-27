		@isset($clients_companies_list)
			<ul class="grid-x grid-padding-x clinets-companies-list small-up-4 medium-up-4 large-up-8">
				@foreach($clients_companies_list as $item)
						<li class="cell clinets-companies-item" data-equalizer-watch>
							<img src="{{ getPhotoPath($item->clientable, 'small') }}" alt="" title="">
							{{-- <span>{{ $item->clientable->name }}</span> --}}
						</li>
				@endforeach
			</ul>
		@endisset