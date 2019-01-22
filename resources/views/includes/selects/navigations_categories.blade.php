{{ Form::select('navigations_category_id', $navigations_categories->pluck('name', 'id'), ($navigations_category_id ?? null), [
	'id' => 'select-navigations_categories',
]
) }}