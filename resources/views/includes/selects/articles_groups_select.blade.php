{!! Form::select('articles_group_id', $articles_groups->pluck('name', 'id'), ($articles_group_id ?? null), ['id' => 'select-articles_groups']) !!}

