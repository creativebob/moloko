{!! Form::select('indicators_category_id', $indicators_categories->pluck('name', 'id'), ($indicators_category_id ?? null), [
	(isset($disabled)) ? 'disabled' : '',

]
) !!}