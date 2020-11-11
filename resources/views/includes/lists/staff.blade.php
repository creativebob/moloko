<ul>
	@foreach ($staff as $staffer)
	<li class="checkbox">
		{!! Form::checkbox('staff[]', $staffer->id, null, ['id' => 'checkbox-staffer-'.$staffer->id]) !!}
		<label for="checkbox-staffer-{{ $staffer->id }}">
            <span>{{ $staffer->user->name }}</span>
        </label>
	</li>
	@endforeach
</ul>
