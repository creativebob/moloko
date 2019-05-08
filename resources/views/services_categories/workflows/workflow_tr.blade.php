<tr class="item" id="table-workflows-{{ $workflow->id }}" data-name="{{ $workflow->process->name }}">
	<td>{{ $workflow->category->name }}</td>
	<td>{{ $workflow->process->name }}</td>
	<td>{{ $workflow->process->description }}</td>
	<td>{{ $workflow->process->group->unit->abbreviation }}</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-item"></a>
	</td>
</tr>
