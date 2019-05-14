<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#settings" aria-selected="true">Информация</a>
            </li>
           {{--  <li class="tabs-title">
                <a data-tabs-target="photos_settings" href="#photos_settings">Настройка</a>
            </li> --}}
        </ul>
    </div>
</div>

{{-- Контейнер для разграничения --}}

<div data-tabs-content="tabs">
    <div class="grid-x grid-padding-x inputs">
        {{-- Первый таб --}}
        <div class="tabs-panel is-active" id="settings">
            <div class="small-12 medium-6 cell tabs-margin-top">

                <div class="grid-x grid-padding-x">

                    <div class="small-12 cell">

                        <label>Название
                            @include('includes.inputs.name', ['required' => true, 'check' => true])
                            <div class="sprite-input-right find-status" id="alias-check"></div>
                            <div class="item-error">Такой склад уже существует!</div>
                        </label>

                    </div>

                    <div class="small-12 cell">

                        <label>Помещение
                            @include('includes.selects.rooms')
                        </label>

                    </div>

                    <div class="small-12 cell">
                        <label>Описание
                            @include('includes.inputs.textarea', ['name' => 'description'])
                        </label>
                    </div>
                </div>
            </div>

            {{-- Чекбоксы управления --}}
            @include('includes.control.checkboxes', ['item' => $stock])

            <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                {{ Form::submit($submit_text, ['class'=>'button']) }}
            </div>
        </div>
        {{-- Конец первого таба --}}

        {{-- Настройки фотографий --}}
        {{-- <div class="tabs-panel" id="photos_settings">
        </div> --}}


    </div>
</div>
{{-- Конец контейнера для разграничения --}}
