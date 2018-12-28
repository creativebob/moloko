<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active"><a href="#settings" aria-selected="true">Информация</a></li>
            <li class="tabs-title"><a data-tabs-target="photos_settings" href="#photos_settings">Настройка фотографий</a></li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">

    <div class="tabs-content" data-tabs-content="tabs">

        {{-- Настройки сущности --}}
        <div class="tabs-panel is-active" id="settings">
            <div class="small-12 medium-7 large-5 cell tabs-margin-top">

                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">
                        <label>Название сущности
                            @include('includes.inputs.text-ru', ['value'=>$entity->name, 'name'=>'name', 'required' => true])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Название сущности в BD
                            @include('includes.inputs.text-en', ['value'=>$entity->alias, 'name'=>'alias', 'required' => true])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Имя модели во фреймворке
                            @include('includes.inputs.text-en', ['value'=>$entity->model, 'name'=>'model', 'required' => true])
                        </label>
                    </div>
                    <div class="small-6 cell radiobutton">Генерировать права?<br>

                        {{ Form::radio('rights_minus', 0, true, ['id'=>'Yes']) }}
                        <label for="Yes"><span>Да</span></label>

                        {{ Form::radio('rights_minus', 1, false, ['id'=>'No']) }}
                        <label for="No"><span>Нет</span></label>

                    </div>

                </div>

            </div>
            {{--     <div class="small-12 medium-5 large-7 cell tabs-margin-top">
            </div> --}}
        </div>

        {{-- Настройки фотографий --}}
        <div class="tabs-panel" id="photos_settings">
            @include('includes.photos_settings.tab', ['item' => $entity])
        </div>
    </div>


    <div class="small-12 cell checkbox">
        {{ Form::checkbox('statistic', 1, $entity->statistic, ['id'=>'statistic-checkbox']) }}
        <label for="statistic-checkbox"><span>Статистика по сущности</span></label>
    </div>

    <div class="small-12 cell checkbox">
        {{ Form::checkbox('dependence', 1, $entity->dependence, ['id'=>'dependence-checkbox']) }}
        <label for="dependence-checkbox"><span>Филиалозависимость</span></label>
    </div>
    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $entity])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {!! Form::submit($submit_text, ['class' => 'button']) !!}
    </div>
</div>

