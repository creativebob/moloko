<tr class="item" id="table-compositions-{{ $composition->id }}" data-name="{{ $composition->name }}">
	<td>{{ $composition->name }}</td>
	<td>{{ $composition->description }}</td>
	<td>{{ $composition->group->unit->abbreviation }}</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-composition"></a>
	</td>
</tr>
