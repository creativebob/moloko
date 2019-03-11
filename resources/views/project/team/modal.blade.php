<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="feedback-modal" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">

						@if(isset($staffer->user->photo_id))

						<div class="text-center">
							<img src="{{ getPhotoPath($staffer->user, 'small') }}" alt="{{ $staffer->position->position_name }}">
						</div>

						@else

						<div class="text-center">Нет фото</div>

						@endif

						<p class="name-person">{{ $staffer->user->name }}</p>
						<p class="status-person">{{ $staffer->position->name }}</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"{{ $staffer->user->quote or 'Нет цитаты...' }}"</blockquote>
					{!! $staffer->user->about or 'Нет информации...' !!}

					<!-- <p>
					@if (isset($staffer->user->about))
					@php
					echo $staffer->user->about;
					@endphp
					@else
					'Нет информации...'
					@endif
				</p> -->

				@include('project.includes.forms.feedback', ['remark' => 'Вопрос с сайта для ' . $staffer->user->name . ':'])

			</div>
		</div>
	</div>
</div>
<button class="close-button" data-close aria-label="Close modal" type="button">
	<span aria-hidden="true" class="icon-close-modal">&times;</span>
</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ