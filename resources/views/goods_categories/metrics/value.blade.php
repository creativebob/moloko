<tr class="item">
	<td class="td-drop"><div class="sprite icon-drop"></div></td>
	<td>{{ $value }}</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-metric-value"></a>
	</td>
	{{ Form::hidden('metric_values[]', $value) }}
</tr>
