
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

		@switch($metric->list_type)

		@case('list')
		<tr>
			<td>Список: {{ $metric->name }}</td>
			<td>{{ $metric->pivot->value }}</td>
		</tr>
		@break

		@case('select')
		<tr>
			<td>Select: {{ $metric->name }}</td>
			<td>{{ $metric->pivot->value }}</td>
		</tr>
		@break

		@endswitch
		
		@break

		@endswitch
