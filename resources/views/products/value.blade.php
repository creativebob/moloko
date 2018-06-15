<tr class="item" id="values-{{ $value }}" data-name="{{ $value }}">
	<td>{{ $value }}</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-value"></a>
	</td>
	{{ Form::hidden('values[]', $value) }}  
</tr>
