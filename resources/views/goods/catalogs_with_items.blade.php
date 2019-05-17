@if ($catalogs_goods->isNotEmpty())
@foreach ($catalogs_goods as $catalog)
<label>{{ $catalog->name }}</label>

{!! Form::select('catalogs_items[]', $catalog->items->pluck('name', 'id'), isset($item->catalogs_items) ? $item->catalogs_items->pluck('id') : null, ['class' => 'chosen-select', 'multiple', 'data-placeholder' => 'Выберите пункты']) !!}

@endforeach
@endif
