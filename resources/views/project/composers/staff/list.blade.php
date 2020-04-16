
		<div class="grid-x grid-padding-x">
			@foreach($staff as $staffer)

				<div class="cell small-6 medium-6 large-3 photo-block">
					<div class="wrap-photo">
						<img src="{{ getPhotoPath($staffer->user) }}" title="" alt="Фото сотрудника">
					</div>
					<span class="employeer-name">{{ $staffer->user->name }}</span>
					<span class="position">{{ $staffer->position->name }}</span>
				</div>

			@endforeach
		</div>