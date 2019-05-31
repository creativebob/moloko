@if ($catalogs_services->isNotEmpty())

@foreach ($catalogs_services as $catalog)
<label>{{ $catalog->name }}</label>

{!! Form::select('catalogs_items[' . $catalog->id . '][]', $catalog->items->pluck('name', 'id'), isset($item->catalogs_items) ? $item->catalogs_items->pluck('id') : null, ['class' => 'chosen-select', 'multiple', 'data-placeholder' => 'Выберите пункты']) !!}

@endforeach
@endif
