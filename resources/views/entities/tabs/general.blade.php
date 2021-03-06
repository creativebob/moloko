<div class="small-12 medium-8 large-8 cell">

    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-6 cell">
            <label>Название сущности
                @include('includes.inputs.name', ['value'=>$entity->name, 'name'=>'name', 'required' => true])
            </label>
        </div>
        <div class="small-12 medium-6 cell">
            <label>Название сущности в BD (Алиас)
                @include('includes.inputs.text-en', ['value'=>$entity->alias, 'name'=>'alias', 'required' => true])
            </label>
        </div>
        <div class="small-12 medium-6 cell">
            <label>Имя модели во фреймворке
                @include('includes.inputs.name', ['value'=>$entity->model, 'name'=>'model', 'required' => true])
            </label>
        </div>
        <div class="small-12 medium-6 cell">
            <label>Путь до шаблона:
                @include('includes.inputs.name', ['value'=>$entity->view_path, 'name'=>'view_path', 'required' => true])
            </label>
        </div>

        <div class="small-12 medium-6 cell">
            <label>Тип:
                {!! Form::select('entities_type_id', $entitiesTypes->pluck('name', 'id'), $entity->entities_type_id, ['placeholder' => 'Без типа']) !!}
            </label>
        </div>

        <div class="small-6 cell radiobutton">Генерировать права?<br>

            {{ Form::radio('rights', 1, true, ['id' => 'Yes']) }}
            <label for="Yes"><span>Да</span></label>

            {{ Form::radio('rights', 0, false, ['id' => 'No']) }}
            <label for="No"><span>Нет</span></label>

        </div>

    </div>

</div>
