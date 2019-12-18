{!! Form::select('directive_category_id', $directive_categories->pluck('name', 'id'), isset($item->directive_category_id) ? $item->directive_category_id : 2, ['id' => 'select-directive_categories']) !!}

