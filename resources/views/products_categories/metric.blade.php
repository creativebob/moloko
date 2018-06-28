<tr class="item" id="metrics-{{ $metric->id }}" data-name="{{ $metric->name }}">
	<td>{{ $metric->name }}</td>
	<td>@if (isset($metric->min)) {{ round($metric->min, 6) }} @endif</td>
	<td>@if (isset($metric->max)) {{ round($metric->max, 6) }} @endif</td>
	<td>{{ $metric->boolean_true }}</td>
	<td>{{ $metric->boolean_false }}</td>
	<td>{{ $metric->color }}</td>
	<td>@if (isset($metric->values))
		<ul>
			@foreach ($metric->values as $value)
			<li>{{ $value->value }}</li>
			@endforeach
		</ul>
	@endif</td>
	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-metric"></a>
	</td>   
</tr>
