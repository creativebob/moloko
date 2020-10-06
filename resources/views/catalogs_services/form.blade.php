<div class="grid-x grid-padding-x inputs tabs-margin-top">
    <div class="cell small-12 medium-7 large-5">

        <div class="grid-x">
            <div class="cell small-12">
                <label>Название
                    @include('includes.inputs.name')
                </label>

                <label>Описание
                    @include('includes.inputs.textarea', ['name' => 'description'])
                </label>

                <fieldset class="fieldset-access">
                    <legend>Филиалы</legend>
                    @include('includes.lists.filials')
                </fieldset>
            </div>

            {{-- <label>Алиас
                @include('includes.inputs.text-en', ['name' => 'alias'])
                <div class="sprite-input-right find-status" id="name-check"></div>
                <div class="item-error">Такой каталог уже существует!</div>
            </label> --}}

            {!! Form::hidden('is_access_page', 0) !!}
            <div class="cell small-12 checkbox">
                {!! Form::checkbox('is_access_page', 1, $catalogs_service->is_access_page, ['id' => 'checkbox-is_access_page']) !!}
                <label for="checkbox-is_access_page"><span>Отображать страницу товара</span></label>
            </div>
        </div>

    </div>
    {{-- <div class="small-12 medium-5 large-7 cell">

        <label>Выберите аватар
            {{ Form::file('photo') }}
        </label>

        <div class="text-center">
            <img id="photo" src="{{ getPhotoPath($catalog) }}">
        </div>
    </div> --}}

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $catalogs_service])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class' => 'button']) }}
    </div>

</div>

