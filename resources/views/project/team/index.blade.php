@extends('project.layouts.app')

@section('title')
<title>{{ $page->title }} | Воротная компания "Марс"</title>
<meta name="description" content="{{ $page->description }} ">
@endsection

@section('content')
<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">Коллектив компании<a href="#anchor-menu" title="Наверх!"><span class="arrow-top"></span></a></h1>
	</div>
	<main class="cell small-12 medium-12 large-12 main-cont">
		<div class="grid-x">
			<!-- 						<h3>Добрые люди работают для добрых людей!</h3> -->

			@isset($staff)

			<ul class="grid-x small-up-1 medium-up-4 large-up-4 teams" data-equalizer data-equalize-by-grid-x="true">

				@foreach ($staff as $staffer)

				<li class="cell">
					<a data-open="AT">
						@if (isset($staffer->user->photo_id))
							<img class="thumbnail"  src="{{ getPhotoPath($staffer->user) }}" alt="{{ $staffer->position->position_name }}">
						@else
							Нет фото
						@endif
						<p class="name-person">{{ $staffer->user->first_name . ' ' . $staffer->user->second_name }}</p>
						<p class="status-person">{{ $staffer->position->name }}</p>
						<span class="myfeedback" id="{{ $staffer->id }}"></span>
					</a>
				</li>

				@endforeach

			</ul>

			@endisset

		</div>

		<section id="modal"></section>
	</main>
</div>

@endsection

@section('scripts')
<script type="text/javascript">

	$(document).on('click', '.myfeedback', function(event) {
		event.preventDefault();
		// alert($(this).attr('id'));

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: 'team/question',
			type: "POST",
			data: {id: $(this).attr('id')},
			success: function(html){
        		// alert(html);
        		$('#modal').html(html);
        		$('#feedback-modal').foundation();
        		$('#feedback-modal').foundation('open');
        	}
        });

	});

	  // ---------------------------------- Закрытие модалки -----------------------------------
	  $(document).on('click', '.icon-close-modal, .submit-edit, .submit-add, .submit-goods-product-add', function() {
	  	$(this).closest('.reveal-overlay').remove();
	  });


	</script>
	@endsection