<div class="grid-x grid-padding-x inputs tabs-margin-top">
    <div class="small-12 medium-7 large-5 cell ">

        <!-- Страница -->
        <label>Название новости
            @include('includes.inputs.name')
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


        <label>Превью новости
            @include('includes.inputs.textarea', ['name' => 'preview', 'value'=>$cur_news->preview])
        </label>

        @include('news.rubricators.rubricators')

        <label>Выберите фото для превью
            {{ Form::file('photo') }}
        </label>

        <div class="text-center">
            <img id="photo" src="{{ getPhotoPath($cur_news) }}">
        </div>

        <label>Слаг
            @include('includes.inputs.varchar', ['name' => 'slug'])
            <div class="sprite-input-right find-status" id="name-check"></div>
            <div class="item-error">Такая новость уже существует!</div>
        </label>

        {{-- @isset($cur_news->alias)
        <a class="button" href="https://{{ $cur_news->site->domain }}/news/{{ $cur_news->alias }}" target="_blank">Просмотр новости</a>
        @endisset --}}



    </div>
    <div class="small-12 medium-5 large-7 cell">

        <label>Контент:
            {{ Form::textarea('content', null, ['id' => 'content-ckeditor']) }}
        </label>

        @include('news.albums.table_albums')

        {{-- Привязка к городам через сайт --}}
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


@section('modals')
<section id="modal"></section>
{{-- Модалка удаления с ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@push('scripts')
@include('includes.scripts.ckeditor')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
@include('includes.scripts.upload-file')

@include('news.scripts')

@include('includes.scripts.delete-from-page-script')
@endpush
