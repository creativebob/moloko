<aside class="task-manager el {{ $open }}" id="task-manager">
	<div class="grid-x tabs-wrap">
		<div class="small-12 cell">
			<ul class="tabs-list" data-tabs id="tabs">
				<li class="tabs-title is-active"><a data-tabs-target="tasks-for-me" aria-selected="true">Задачи мне</a></li>
				<li class="tabs-title"><a data-tabs-target="tasks-from-me">Задачи от меня</a></li>
			</ul>
		</div>
	</div>
	<div class="grid-x tabs-wrap period-task">
		<div class="small-12 cell">
			<div class="tabs-content" data-tabs-content="tabs" id="portal-challenges-for-me">

				{{-- Менеджер задач --}}
				@include('layouts.challenges_for_me')

			</div>
		</div>
	</div>
</aside>


@push('scripts')
	<script type="text/javascript">

		function get_challenges(){

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: '/admin/get_challenges_user',
				type: "POST",
				success: function(html){

					$('#portal-challenges-for-me').html(html);

					Foundation.reInit($('#tabs-period-for-me'));
					Foundation.reInit($('#tabs-period-from-me'));

					var elem = new Foundation.Tabs($('#tabs-period-for-me'));
					var elem2 = new Foundation.Tabs($('#tabs-period-from-me'));

				}
			});
		}

	</script>
	@endpush

