<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">

            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Общая информация</a>
            </li>

            <li class="tabs-title">
                <a data-tabs-target="tab-seo" href="#tab-seo">SEO</a>
            </li>

        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="tabs-panel is-active" id="tab-general">
                <div class="grid-x grid-padding-x">
                <div class="small-12 medium-7 large-5 cell ">

                    <label>Название страницы
                        @include('includes.inputs.name', ['name' => 'name', 'required' => true])
                    </label>

{{--                    <label>Заголовок страницы (Title)--}}
{{--                        @include('includes.inputs.varchar', ['name' => 'title', 'required' => true])--}}
{{--                    </label>--}}

                    <label>Подзаголовок страницы
                        @include('includes.inputs.varchar', ['name' => 'subtitle'])
                    </label>

{{--                    <label>Заголовок (H1)--}}
{{--                        @include('includes.inputs.varchar', ['name' => 'header'])--}}
{{--                    </label>--}}

{{--                    <label>Описание страницы (Description)--}}
{{--                        @include('includes.inputs.textarea', ['name' => 'description'])--}}
{{--                    </label>--}}

{{--                    <label>Список ключевых слов (Keywords)--}}
{{--                        @include('includes.inputs.varchar', ['name' => 'keywords'])--}}
{{--                    </label>--}}

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
            </div>

            {{-- SEO --}}
            <div class="tabs-panel" id="tab-seo">
                @include('system.common.tabs.seo', ['seo' => $page->seo])
            </div>

        </div>
    </div>
</div>

