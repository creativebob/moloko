<div class="grid-x grid-padding-x inputs tabs-margin-top">
    <div class="small-12 medium-7 large-5 cell ">
        @if ($errors->any())
        <div class="alert callout" data-closable>
            <h5>Неправильный формат данных:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <!-- Страница -->
        <label>Название новости
            @include('includes.inputs.string', ['name'=>'name', 'value'=>$cur_news->name, 'required' => true])
        </label>
        <div class="grid-x grid-padding-x">
            <div class="small-6 cell">
                <label>Начало публикации
                    @include('includes.inputs.date', ['name'=>'publish_begin_date', 'value'=>$cur_news->publish_begin_date, 'required' => true])
                </label>
            </div>
            <div class="small-6 cell">
                <label>Окончание публикации
                    @include('includes.inputs.date', ['name'=>'publish_end_date', 'value'=>$cur_news->publish_end_date])
                </label>
            </div>
        </div>
        <label>Заголовок новости
            @include('includes.inputs.string', ['name'=>'title', 'value'=>$cur_news->title, 'required' => true])
        </label>
        <label>Превью новости
            @include('includes.inputs.textarea', ['name'=>'preview', 'value'=>$cur_news->preview])
        </label>

        <label>Выберите фото для превью
            {{ Form::file('photo') }}
        </label>
        <div class="text-center">
            <img id="photo" @if (isset($cur_news->photo_id)) src="/storage/{{ $cur_news->company->id }}/media/news/{{ $cur_news->id }}/img/original/{{ $cur_news->photo->name }}" @endif>
        </div>
        <label>Алиас новости
            @include('includes.inputs.varchar', ['name'=>'alias', 'value'=>$cur_news->alias, 'required' => true])
            <div class="sprite-input-right find-status" id="name-check"></div>
            <div class="item-error">Такая новость уже существует!</div>
        </label>
        @isset($cur_news->alias)
        <a class="button" href="https://{{ $cur_news->site->domain }}/news/{{ $cur_news->alias }}" target="_blank">Просмотр новости</a>
        @endisset
        {{ Form::hidden('check', 0, ['id'=>'check']) }}
        {{ Form::hidden('site_id', $site->id) }}
    </div>
    <div class="small-12 medium-5 large-7 cell">
        <label>Контент:
            {{ Form::textarea('content', $cur_news->сontent, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
        </label>
        <table class="table-content tabs-margin-top">
            <caption>Прикрепленные альбомы</caption>
            <thead>
                <tr>
                    <th>Альбом</th>
                    <th>Категория</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($cur_news->albums))
                @foreach ($cur_news->albums as $album)
                @include('news.albums', $album)
                @endforeach
                @endif
            </tbody>
        </table>
        {{ Form::hidden('cur_news_id', $cur_news->id, ['id' => 'cur-news-id']) }}
        <div class="text-center">
            <a class="button tabs-margin-top" data-open="album-add">Прикрепить альбом</a>
        </div>

        <div class="grid-x">
            <div class="small-12 medium-6 cell">
                <div class="checkboxer-wrap">
                    <div class="checkboxer-toggle" data-toggle="dropdown-city" data-name="">
                        <div class="checkboxer-title">
                            <span class="title">Выбор города</span>
                        </div>
                        <div class="checkboxer-button">
                            <span class="sprite icon-checkboxer"></span>
                        </div>
                    </div>
                </div>
                <div class="dropdown-pane checkboxer-pane hover" data-position="bottom" data-alignment="left" id="dropdown-city" data-dropdown data-auto-focus="true" data-close-on-click="true" data-h-offset="-17" data-v-offset="2">

                    <ul class="checkbox">
                        @foreach ($filials as $filial)
                        <li>
                            {{ Form::checkbox('cities[]', $filial->location->city_id, null, ['id' => 'city-'.$filial->location->city_id]) }}
                            <label for="city-{{ $filial->location->city_id }}"><span>{{ $filial->location->city->name }}</span></label>
                        </li>
                        @endforeach
                    </ul>

                </div>
            </div>
        </div>
    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $cur_news])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
</div>


