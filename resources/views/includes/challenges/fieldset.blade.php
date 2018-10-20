@can ('index', App\Challenge::class)
<fieldset class="fieldset-challenge">
	<legend>Задачи:</legend>
	<div class="grid-x grid-padding-x"> 
		<table class="table-challenges" id="table-challenges">
			<thead>
				<tr>
					<th>Задача</th>
					<th>Дата</th>
					<th>Описание</th>
					<th></th>
				</tr>
			</thead>
			<tbody id="challenges-list">

				@if (count($item->challenges) > 0)
				@include('includes.challenges.challenges', ['challenges' => $item->challenges])
				@endif
				
			</tbody>
		</table>
	</div>
	@can ('create', App\Challenge::class)
	<div class="grid-x grid-padding-x align-left">
		<div class="small-4 cell">
			@can('update', $lead)
				<a class="button green-button" data-open="challenge-add">Добавить</a>
			@endcan
		</div>
	</div>
	@endcan
</fieldset>
@endcan