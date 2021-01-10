<div class="grid-x grid-padding-x inputs tabs-margin-top">
    <div class="small-12 medium-7 large-5 cell ">

        <label>Название страницы
            @include('includes.inputs.name', ['name' => 'name', 'required' => true])
        </label>

        <label>Заголовок страницы (Title)
            @include('includes.inputs.string', ['name' => 'title', 'required' => true])
        </label>

        <label>Подзаголовок страницы
            @include('includes.inputs.string', ['name' => 'subtitle'])
        </label>

        <label>Заголовок (H1)
            @include('includes.inputs.string', ['name' => 'header'])
        </label>

        <label>Описание страницы (Description)
            @include('includes.inputs.textarea', ['name' => 'description'])
        </label>

        <label>Список ключевых слов (Keywords)
            {!! Form::text('keywords', null, ['maxlength'=>'250', 'autocomplete'=>'off', 'data']) !!}
        </label>

        <label>Алиас страницы
            @include('includes.inputs.text-en', ['name' => 'alias'])
            <div class="sprite-input-right find-status" id="alias-check"></div>
            <div class="item-error">Такая страница уже существует!</div>
        </label>

        <label>Видео
            {{ Form::text('video_url', $page->video_url, []) }}
        </label>

        <label>Блок видео
            @include('includes.inputs.textarea', ['name' => 'video'])
        </label>

        <label>Контент:
            {{ Form::textarea('content', $page->content, ['id' => 'content-ckeditor', 'autocomplete' => 'off', 'size' => '10x3']) }}
        </label>

    </div>
    <div class="small-12 medium-5 large-7 cell">

        <label>Выберите аватар
            {{ Form::file('photo') }}
        </label>

        <div class="text-center">
            <img id="photo" src="{{ getPhotoPath($page) }}">
        </div>
    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $page])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class' => 'button']) }}
    </div>

</div>

