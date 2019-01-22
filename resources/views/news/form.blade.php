<div class="grid-x grid-padding-x inputs tabs-margin-top">
    <div class="small-12 medium-7 large-5 cell ">

        <!-- Страница -->
        <label>Название новости
            @include('includes.inputs.string', ['name'=>'name', 'value' => $cur_news->name, 'required' => true])
        </label>
        <div class="grid-x grid-padding-x">
            <div class="small-6 cell">
                <label>Начало публикации
                    @include('includes.inputs.date', [
                        'name' => 'publish_begin_date',
                        'value' => isset($cur_news->publish_begin_date) ? $cur_news->publish_begin_date->format('d.m.Y') : '',
                        'required' => true
                    ]
                    )
                </label>
            </div>
            <div class="small-6 cell">
                <label>Окончание публикации
                    @include('includes.inputs.date', [
                        'name' => 'publish_end_date',
                        'value' => isset($cur_news->publish_end_date) ? $cur_news->publish_end_date->format('d.m.Y') : ''
                        ]
                        )
                </label>
            </div>
        </div>

        <label>Заголовок новости
            @include('includes.inputs.string', ['name' => 'title', 'value'=>$cur_news->title, 'required' => true])
        </label>

        <label>Превью новости
            @include('includes.inputs.textarea', ['name'=>'preview', 'value'=>$cur_news->preview])
        </label>

        <label>Выберите фото для превью
            {{ Form::file('photo') }}
        </label>

        <div class="text-center">
            <img id="photo" src="{{ getPhotoPath($cur_news) }}">
        </div>

        <label>Алиас новости
            @include('includes.inputs.varchar', ['name'=>'alias', 'value'=>$cur_news->alias])
            <div class="sprite-input-right find-status" id="name-check"></div>
            <div class="item-error">Такая новость уже существует!</div>
        </label>

        {{-- @isset($cur_news->alias)
        <a class="button" href="https://{{ $cur_news->site->domain }}/news/{{ $cur_news->alias }}" target="_blank">Просмотр новости</a>
        @endisset --}}



    </div>
    <div class="small-12 medium-5 large-7 cell">

        <label>Контент:
            {{ Form::textarea('content', $cur_news->сontent, ['id'=>'content-ckeditor']) }}
        </label>

        <table class="content-table tabs-margin-top">
            <caption>Прикрепленные альбомы</caption>
            <thead>
                <tr>
                    <th>Альбом</th>
                    <th>Категория</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="table-albums">
                @if (isset($cur_news->albums))
                @foreach ($cur_news->albums as $album)
                @include('news.album', $album)
                @endforeach
                @endif
            </tbody>
        </table>

        <div class="text-center">
            <a class="button tabs-margin-top" data-open="album-add">Прикрепить альбом</a>
        </div>

        @isset ($site->departments)
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



                        @foreach ($site->departments as $department)
                        <li>
                            {{ Form::checkbox('cities[]', $department->location->city_id, null, ['id' => 'city-'.$department->location->city_id]) }}
                            <label for="city-{{ $department->location->city_id }}"><span>{{ $department->location->city->name }}</span></label>
                        </li>
                        @endforeach

                    </ul>

                </div>
            </div>
        </div>
        @endisset
    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $cur_news])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class'=>'button']) }}
    </div>
</div>




