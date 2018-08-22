<table>
	<tbody>
		@switch($metric->property->type)
		@case('numeric')
		<tr>
			<td>{{ $metric->name }}</td>
			<td>{{ $metric->pivot->value }}</td>
		</tr>
		@break

		@case('percent')
		<tr>
			<td>{{ $metric->name }}</td>
			<td>{{ $metric->pivot->value }}</td>
		</tr>
		@break

		@case('list')
		<label>
			@switch($metric->list_type)
			@case('list')
			<a data-toggle="metric-{{ $metric->id }}-dropdown">Список: {{ $metric->name }}</a>
			<div class="dropdown-pane" id="metric-{{ $metric->id }}-dropdown" data-dropdown data-position="bottom" data-alignment="left" data-close-on-click="true">
				<ul>

					@foreach ($metric->values as $value)
					<li class="checkbox">
						{{ Form::checkbox('metrics-'.$metric->id.'[]', $value->value, null, ['id' => 'add-metric-value-'. $value->id]) }}
						<label for="add-metric-value-{{ $value->id }}"><span>{{ $value->value }}</span></label>
					</li>
					@endforeach

				</ul>
			</div>
			@break

			@case('select')
			<tr>
				<td>{{ $metric->name }}</td>
				<td>{{ $metric->pivot->value }}</td>
			</tr>
			@break
			@endswitch
		</label>
		@break


		@endswitch
	</tbody>
</table>