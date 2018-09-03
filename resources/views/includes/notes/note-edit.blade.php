<tr class="item" id="notes-{{ $note->id }}" data-name="{{ $note->body }}">
	<td>
		<span class="note_date">{{ $note->created_at->format('d.m.Y') }}</span>
		<br>
		<span class="note_time">{{ $note->created_at->format('H:i') }}</span>
	</td>

	<td>
		@include('includes.inputs.textarea', ['name'=>'edit_body', 'value'=>$note->body, 'required'=>''])
	</td>

	<td class="actions">
		@can('update', $note)
		<a class="button" id="edit-note">Редактировать</a>
		@endcan
	</td>
</tr>