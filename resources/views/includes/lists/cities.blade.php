<ul>
	@foreach ($cities as $city)
	<li class="checkbox">
		{{ Form::checkbox('cities[]', $city->id, null, ['id' => 'checkbox-city-'.$city->id]) }}
		<label for="checkbox-city-{{ $city->id }}"><span>{{ $city->name }}</span></label>
	</li>
	@endforeach
</ul>
