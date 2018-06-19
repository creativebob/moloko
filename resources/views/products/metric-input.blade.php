@switch($metric->property->type)
@case('numeric')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->property->name }}</span>
	{{ Form::number('metrics-'.$metric->id) }}
</label>
@break


@default
<span>Something went wrong, please try again</span>
@endswitch

