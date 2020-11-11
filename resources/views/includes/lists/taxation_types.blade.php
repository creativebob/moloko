<ul>
	@foreach ($taxationTypes as $taxationType)
	<li class="checkbox">
		{!! Form::checkbox('taxation_types[]', $taxationType->id, null, ['id'=>'checkbox-taxation_type-'.$taxationType->id]) !!}
		<label for="checkbox-taxation_type-{{ $taxationType->id }}">
            <span>{{ $taxationType->name }}</span>
        </label>
	</li>
	@endforeach
</ul>
