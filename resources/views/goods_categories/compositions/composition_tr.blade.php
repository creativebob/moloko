<tr class="item" id="table-compositions-{{ $composition->id }}" data-name="{{ $composition->article->name }}">
	<td>{{ $composition->category->name }}</td>
	<td>{{ $composition->article->name }}</td>
	<td>{{ $composition->article->description }}</td>
	<td>{{ $composition->article->group->unit->abbreviation }}</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-composition"></a>
	</td>
</tr>
