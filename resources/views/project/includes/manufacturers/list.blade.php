		@isset($manufacturers_list)
			<ul class="grid-x grid-padding-x manufacturers-list align-center">
				@foreach($manufacturers_list as $manufacturer)
					<li class="cell small-12 medium-6 large-12">
						<div class="grid-x">
							<div class="cell small-12 large-3 wrap-photo-manufacturer">
								<img src="{{ getPhotoPath($manufacturer->company, 'small') }}" alt="" title="">
							</div>
							<div class="cell small-12 large-9">
								<div class="grid-x grid-padding-x">
									<div class="cell small-12 large-9">
										<h3 class="h3-name-manufacturer">{{ $manufacturer->company->designation ?? $manufacturer->company->name }}</h3>
									</div>
									<div class="cell small-12 large-8">
										<p>{{ $manufacturer->company->about }}</p>
										@if(!empty($manufacturer->company->location->address))
											<p class="address-company">{{ $manufacturer->company->location->city->name }}, 
												{{ $manufacturer->company->location->address }}<br>
												Тел: 
												<span>{{ decorPhone($manufacturer->company->main_phone->phone) }}</span>
											</p>
										@endif
									</div>
									<div class="cell small-12 large-3 attachments">
										@foreach($manufacturer->attachments as $attachment)
											<p>{{ $attachment->article->name ?? '' }}</p>
											<p>{{ $attachment->article->description ?? '' }}</p>
										@endforeach
									</div>
								</div>
							</div>
						</div>
					</li>
				@endforeach
			</ul>
		@endisset