@can ('index', App\Note::class)
<fieldset class="fieldset-notes">
	<legend>События:</legend>
	<div class="grid-x grid-padding-x"> 
		<table class="table-notes" id="table-notes">
			<thead>
				<tr>
					<th>Время</th>
					<th>Событие</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@can ('create', App\Note::class)
				<tr id="tr-add-note">
					<td>
						<span class="note_date">{{ date('d.m.Y') }}</span>
					</td>

					<td class="body">
						@include('includes.inputs.textarea', ['name'=>'add_body', 'value'=>null, 'required'=>''])
					</td>
					<td class="actions"><a class="button" id="add-note">Добавить</a></td>
				</tr>
				@endcan

				@if (count($item->notes) > 0)

				@foreach ($item->notes as $note)
					@include('includes.notes.note', ['note' => $note])
				@endforeach

				@endif

			</tbody>
		</table>
	</div>
</fieldset> 
@endcan