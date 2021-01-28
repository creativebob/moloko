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
            <div class="small-12 medium-8 large-8 cell tabs-margin-top">

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
                    <div class="small-6 cell radiobutton">Генерировать права?<br>

                        {{ Form::radio('rights', 1, true, ['id' => 'Yes']) }}
                        <label for="Yes"><span>Да</span></label>

                        {{ Form::radio('rights', 0, false, ['id' => 'No']) }}
                        <label for="No"><span>Нет</span></label>

                    </div>

                </div>

            </div>
            {{--     <div class="small-12 medium-5 large-7 cell tabs-margin-top">
            </div> --}}
        </div>

        {{-- Настройки тмц --}}
        {{-- <li class="tabs-title"><a data-tabs-target="tmc" href="#tmc">ТМЦ</a></li> --}}
        {{-- <div class="tabs-panel" id="tmc">
            <div class="small-12 medium-7 large-5 cell tabs-margin-top">

                <div class="grid-x grid-padding-x">
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('tmc', 1, null, ['id' => 'tmc-checkbox']) }}
                        <label for="tmc-checkbox"><span>ТМЦ</span></label>
                    </div>
                    <div class="small-12 cell">
                        <label>Состав
                            @include('includes.selects.tmc')
                        </label>
                    </div>



                </div>

            </div>
        </div> --}}

        {{-- Настройки фотографий --}}
        <div class="tabs-panel" id="photos_settings">
            @include('system.pages.settings.photo_settings.tabs.settings', ['photoSetting' => $entity->photo_settings])
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

