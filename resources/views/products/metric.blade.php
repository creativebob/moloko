<tr class="item" id="metrics-{{ $metric->id }}" data-name="{{ $metric->name }}">
	<td>{{ $metric->name }}</td>
	<td>{{ $metric->min }}</td>
	<td>{{ $metric->max }}</td>
	<td>{{ $metric->boolean_true }}</td>
	<td>{{ $metric->boolean_false }}</td>
	<td>{{ $metric->color }}</td>
	<td>{{ $metric->booklist_id }}</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="item-delete-ajax"></a>
	</td>   
	{{ Form::hidden('metrics[]', $metric->id, ['form' => 'product-form']) }}
</tr>
