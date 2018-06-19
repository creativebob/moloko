<tr class="item" id="metrics-{{ $metric->id }}" data-name="{{ $metric->name }}">
	<td>{{ $metric->name }}</td>
	<td>{{ round($metric->min, 6) }}</td>
	<td>{{ round($metric->max, 6) }}</td>
	<td>{{ $metric->boolean_true }}</td>
	<td>{{ $metric->boolean_false }}</td>
	<td>{{ $metric->color }}</td>
	<td>{{ $metric->booklist_id }}</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-metric"></a>
	</td>   
</tr>
