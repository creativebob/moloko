{!! Form::select('unit_id', $units->pluck('name', 'id'), isset($article->unit_id) ? $article->unit_id : $article->group->unit_id, ['id' => 'select-units', isset($disabled) ? 'disabled' : '']) !!}
