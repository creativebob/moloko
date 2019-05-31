{!! Form::select('unit_id', $units->pluck('name', 'id'), $article->group->unit_id, ['id' => 'select-units', isset($disabled) ? 'disabled' : '']) !!}
