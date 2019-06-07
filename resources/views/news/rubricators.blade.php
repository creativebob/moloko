<div class="grid-x grid-padding-x">
    <div class="small-6 cell">
        <label>Рубрика
            {!! Form::select('rubricator_id', $rubricators->pluck('name', 'id'), null, ['id' => 'select-rubricators']) !!}
        </label>
    </div>
    <div class="small-6 cell">
        <label>Пункт рубрики
            {!! Form::select('rubricators_item_id', isset($cur_news->rubricator_id) ? $rubricators->firstWhere('id', $cur_news->rubricator_id)->items->pluck('name', 'id') : $rubricators->first()->items->pluck('name', 'id'), null, ['id' => 'select-rubricators_items']) !!}
        </label>
    </div>
</div>
