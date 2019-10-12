		@isset($manufacturers_list)
			<ul class="grid-x grid-padding-x manufacturers-list align-center">
				@foreach($manufacturers_list as $manufacturer)
					<li class="cell small-12">
						<div class="grid-x">
							<div class="small-12 large-3">
								<img src="{{ getPhotoPath($manufacturer->company, 'small') }}" alt="" title="">
							</div>
							<div class="small-12 large-6">
								<h3>{{ $manufacturer->company->designation ?? $manufacturer->company->name }}</h3>
								<p>{{ $manufacturer->company->about }}</p>
							</div>
						</div>
					</li>
				@endforeach
			</ul>
		@endisset