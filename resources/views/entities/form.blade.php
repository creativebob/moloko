<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active"><a href="#tab-general" aria-selected="true">Информация</a></li>

            <li class="tabs-title"><a data-tabs-target="tab-photos_settings" href="#tab-photos_settings">Настройка фотографий</a></li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs tabs-margin-top">

    <div class="tabs-content" data-tabs-content="tabs">

        {{-- Настройки сущности --}}
        <div class="tabs-panel is-active" id="tab-general">

            @include('entities.tabs.general')
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
        <div class="tabs-panel" id="tab-photos_settings">
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

