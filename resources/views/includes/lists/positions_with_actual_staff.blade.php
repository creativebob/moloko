<ul>
	@foreach ($positions as $position)
	<li class="checkbox">
		{{ Form::checkbox('positions[]', $position->id, null, ['id' => "checkbox-position-{$position->id}"]) }}
		<label for="checkbox-position-{{ $position->id }}">
            <span>{{ $position->name }}</span>
        </label>
	</li>
	@endforeach
</ul>
