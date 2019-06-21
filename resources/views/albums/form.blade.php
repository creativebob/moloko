<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active"><a href="#settings" aria-selected="true">Информация об альбоме</a></li>
            <li class="tabs-title"><a data-tabs-target="photos_settings" href="#photos_settings">Настройка</a></li>
        </ul>
    </div>
</div>

{{-- Контейнер для разграничения --}}
<div class="grid-x grid-padding-x inputs">
    <div data-tabs-content="tabs">

        {{-- Первый таб --}}
        <div class="tabs-panel is-active" id="settings">
            <div class="small-12 medium-6 cell tabs-margin-top">

                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">

                        <label>Название
                            @include('includes.inputs.name', ['required' => true, 'check' => true])
                            <div class="sprite-input-right find-status" id="alias-check"></div>
                            <div class="item-error">Такой альбом уже существует!</div>
                        </label>

                        <label class="alias">Слаг
                            @include('includes.inputs.name', ['name' => 'slug', 'check' => true])
                            <div class="sprite-input-right find-status" id="alias-check"></div>
                            <div class="item-error">Альбом с таким алиасом уже существует!</div>
                        </label>

                    </div>

                    <div class="small-12 medium-6 cell">

                        <label>Категория
                            @include('albums.select_albums_categories', ['parent_id' => $album->category_id])
                        </label>

                        <label>Задержка времени (для слайдера), сек
                            {{ Form::text('delay', $album->delay, ['class'=>'digit-2-field', 'maxlength'=>'2', 'autocomplete'=>'off']) }}
                            <div class="sprite-input-right find-status" id="name-check"></div>
                            <span class="form-error">Введите кол-во секунд</span>
                        </label>

                    </div>

                    <div class="small-12 cell">
                        <label>Описание
                            @include('includes.inputs.textarea', ['name' => 'description'])
                        </label>
                    </div>
                </div>
            </div>

            <div class="small-12 medium-5 large-7 cell tabs-margin-top">

                @isset ($album->photo_id)
                <img src="{{ getPhotoPath($album) }}">
                @endisset

            </div>

            <div class="small-12 small-text-center cell checkbox">
                {{ Form::checkbox('personal', 1, null, ['id' => 'personal-checkbox']) }}
                <label for="personal-checkbox"><span>Личный альбом</span></label>
            </div>

            {{-- Чекбоксы управления --}}
            @include('includes.control.checkboxes', ['item' => $album])

            <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                {{ Form::submit($submit_text, ['class' => 'button']) }}
            </div>
        </div>
        {{-- Конец первого таба --}}

        {{-- Настройки фотографий --}}
        <div class="tabs-panel" id="photos_settings">
            @include('albums.photo_settings', ['item' => $album])
        </div>

    </div>
</div>
{{-- Конец контейнера для разграничения --}}
