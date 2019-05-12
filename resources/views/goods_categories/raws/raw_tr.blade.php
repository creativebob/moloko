<tr class="item" id="table-raws-{{ $raw->id }}" data-name="{{ $raw->article->name }}">
	<td>{{ $raw->category->name }}</td>
	<td>{{ $raw->article->name }}</td>
	<td>{{ $raw->article->description }}</td>
	<td>{{ $raw->article->group->unit->abbreviation }}</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-item"></a>
	</td>
</tr>