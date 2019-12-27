<div class="grid-x grid-padding-x inputs tabs-margin-top">
    <div class="small-12 medium-7 large-5 cell ">

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

        {{-- <label>Алиас
            @include('includes.inputs.text-en', ['name' => 'alias'])
            <div class="sprite-input-right find-status" id="name-check"></div>
            <div class="item-error">Такой каталог уже существует!</div>
        </label> --}}

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
    @include('includes.control.checkboxes', ['item' => $catalogs_goods])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class' => 'button']) }}
    </div>

</div>

