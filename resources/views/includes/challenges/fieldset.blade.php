@can ('index', App\Challenge::class)
<fieldset class="fieldset-access">
	<legend>Задачи:</legend>
	<div class="grid-x grid-padding-x"> 
		<table class="table-challenges" id="table-challenges">
			<thead>
				<tr>
					<th>Задача</th>
					<th>Дата</th>
					<th>Время</th>
					<th>Описание</th>
					<th>Исполнитель</th>
				</tr>
			</thead>
			<tbody>

				@if (count($item->challenges) > 0)

				@foreach ($item->challenges as $challenge)
				@include('includes.challenges.challenge', ['challenge' => $challenge])
				@endforeach

				@endif
				

			</tbody>
		</table>
	</div>
	@can ('create', App\Challenge::class)
	<div class="grid-x grid-padding-x align-center">
		<div class="small-4 cell">
			<a class="button" data-open="add-challenge">Добавить</a>
		</div>
	</div>
	@endcan
</fieldset>
@endcan