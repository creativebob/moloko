<tr class="item @if ($note->author_id == 1) note-robot @endif" id="notes-{{ $note->id }}" data-name="{{ $note->body }}">

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
		@if($note->author_id != 1)
		@can('update', $note)
		<div class="icon-edit sprite" data-open="note-edit"></div>
		@endcan
		@endif

		@if($note->author_id != 1)
		@can ('delete', $note)
		<div class="icon-delete sprite" data-open="item-delete-ajax"></div>
		@endcan
		@endif
	</td>
</tr>