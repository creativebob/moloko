<div class="grid-x grid-padding-x">
    <div class="cell small-12 medium-6">

        <div class="grid-x grid-padding-x">

            <div class="cell small-12 medium-6">

                <label>Название
                    @include('includes.inputs.name', ['required' => true, 'check' => true])
                    <div class="sprite-input-right find-status" id="alias-check"></div>
                    <div class="item-error">Такой альбом уже существует!</div>
                </label>

                <label class="alias">Алиас
                    @include('includes.inputs.name', ['name' => 'alias'])
                    <div class="sprite-input-right find-status" id="alias-check"></div>
                </label>

                <label class="alias">Слаг
                    @include('includes.inputs.name', ['name' => 'slug', 'check' => true])
                    <div class="sprite-input-right find-status" id="alias-check"></div>
                    <div class="item-error">Альбом с таким алиасом уже существует!</div>
                </label>

            </div>

            <div class="small-12 medium-6 cell">

                <label>Категория
                    @include('system.pages.marketings.albums.select_albums_categories', ['parent_id' => $album->category_id])
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
        {{ Form::hidden('personal', 0) }}
        {{ Form::checkbox('personal', 1, null, ['id' => 'personal-checkbox']) }}
        <label for="personal-checkbox"><span>Личный альбом</span></label>
    </div>
</div>
