<tr class="item" id="rules-{{ $rule->id }}" data-name="{{ $rule->name }}">
	<td>{{ $rule->field->entity->name }}</td>
	<td>{{ $rule->field->name }}</td>
	<td>{{ $rule->name }}</td>
	<td>{{ $rule->description }}</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-rule"></a>
	</td>   
</tr>
