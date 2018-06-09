

@switch($type)
    @case('numeric')
    <label class="small-12 cell">Название
@include('includes.inputs.name', ['value'=>null, 'name'=>'metric_name', 'required'=>'required'])
</label>

<label class="small-6 cell">Минимум
	{{ Form::number('metric_min') }}
</label>
<label class="small-6 cell">Максимум
	{{ Form::number('metric_max') }}
</label>
<label class="small-6 cell">Единица измерения
	{{ Form::select('metric_unit_id', $units_list, null) }}
</label>
<label class="small-6 cell">Знаки после запятой
	{{ Form::select('metric_lol', ['1' => '0.0', '2' => '0.00', '3' => '0.000'], null) }}
</label>


        @break

   

    @default
        Default case...
@endswitch
<div class="small-12 cell text-center">
<a class="button" id="add-metric">Добавить метрику</a>
</div>

