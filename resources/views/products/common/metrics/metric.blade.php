<tr class="item" id="table-metrics-{{ $metric->id }}" data-name="{{ $metric->name }}">
	<td>{{ $metric->name }}</td>
	<td>@isset ($metric->min) {{ number_format($metric->min, $metric->decimal_place) }} @endisset</td>
	<td>@isset ($metric->max) {{ number_format($metric->max, $metric->decimal_place) }} @endisset</td>
	<td>{{ $metric->boolean_true }}</td>
	<td>{{ $metric->boolean_false }}</td>
	<td>{{ $metric->color }}</td>
	<td>@isset($metric->values)
		<ul>
			@foreach ($metric->values as $value)
			<li>{{ $value->value }}</li>
			@endforeach
		</ul>
	@endisset</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-metric"></a>
	</td>
</tr>
