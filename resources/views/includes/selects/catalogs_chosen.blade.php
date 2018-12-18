{{-- {!! Form::select('catalogs[]', $catalogs_list->pluck('name', 'id'), $item->catalogs, [
	'class' => 'chosen-select',
	'multiple'
]
) !!} --}}

<select name="catalogs[]" data-placeholder="Выберите каталоги..." multiple class="chosen-select">
	{!! $catalogs_list !!}
</select>