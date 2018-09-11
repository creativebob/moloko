<tr class="item" id="notes-{{ $note->id }}" data-name="{{ $note->body }}">

	<td>
		<span class="note_date">{{ $note->created_at->format('d.m.Y') }}</span>
		<br>
		<span class="note_time">{{ $note->created_at->format('H:i') }}</span>
	</td>

	<td class="body">
		@php 
		echo str_replace("\n", '<br>', $note->body);
		@endphp
	</td>

	<td class="actions">
		@can('update', $note)
		<div class="icon-edit sprite" data-open="note-edit"></div>
		@endcan

		@can ('delete', $note)
		<div class="icon-delete sprite" data-open="item-delete-ajax"></div>
		@endcan
	</td>
</tr>