@switch($metric->property->type)
@case('numeric')
<label>{{ $metric->property->name }}
	{{ Form::number('metrics-'.$metric->id) }}
</label>
@break


@default
<span>Something went wrong, please try again</span>
@endswitch

