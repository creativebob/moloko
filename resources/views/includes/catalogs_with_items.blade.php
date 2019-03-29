@foreach ($catalogs as $catalog)
<label>{{ $catalog->name }}</label>

{!! Form::select('catalogs_items[]', $catalog->items->pluck('name', 'id'), null, ['class' => 'chosen-select', 'multiple', 'data-placeholder' => 'Выберите пункты']) !!}

@endforeach