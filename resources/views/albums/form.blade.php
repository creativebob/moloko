



<div class="grid-x tabs-wrap">
  <div class="small-12 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Информация об альбоме</a></li>
      <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Настройка</a></li>
    </ul>
  </div>
</div>

 {{-- Контейнер для разграничения --}}
<div class="grid-x grid-padding-x inputs">

        {{-- Контейнер для табов --}}
        <div data-tabs-content="tabs">

            {{-- Блок вывода ошибок --}}
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


            {{-- Первый таб --}}
            <div class="tabs-panel is-active" id="content-panel-1">
                <div class="small-12 medium-6 cell tabs-margin-top">
                    <div class="grid-x grid-padding-x">
                        <div class="small-12 medium-6 cell">
                            <label>Название альбома
                                @include('includes.inputs.name', ['value'=>$album->name, 'name'=>'name', 'required'=>'required'])
                            </label>
                            <label class="alias">Алиас альбома
                                @include('includes.inputs.name', ['value'=>$album->alias, 'name'=>'alias', 'required'=>'required'])
                                <div class="sprite-input-right find-status" id="alias-check"></div>
                                <div class="item-error">Такой альбом уже существует!</div>
                            </label>
                        </div>
                        <div class="small-12 medium-6 cell">
                            <label>Категория альбома
                                <select name="albums_category_id">
                                    @php
                                        echo $albums_categories_list;
                                    @endphp
                                </select>
                            </label>
                            <label>Задержка времени (для слайдера), сек
                                {{ Form::text('delay', $album->delay, ['class'=>'digit-2-field', 'maxlength'=>'2', 'autocomplete'=>'off']) }}
                                <div class="sprite-input-right find-status" id="name-check"></div>
                                <span class="form-error">Введите кол-во секунд</span>
                            </label>
                        </div>
                        <div class="small-12 cell">
                            <label>Описание альбома
                                @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$album->description, 'required'=>''])
                            </label>
                        </div>
                    </div>
                </div>

                <div class="small-12 medium-5 large-7 cell tabs-margin-top">
                    @if (isset($album->avatar))
                        <img src="/storage/{{ $album->company->company_alias }}/media/albums/{{ $album->alias }}/{{ $album->avatar }}">
                    @endif
                </div>

                <div class="small-12 small-text-center cell checkbox">
                    {{ Form::checkbox('access', 1, null, ['id'=>'private-checkbox']) }}
                    <label for="private-checkbox"><span>Личный альбом.</span></label>
                </div>

                {{-- Чекбокс отображения на сайте --}}
                @can ('publisher', $album)
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('display', 1, $album->display, ['id' => 'display']) }}
                        <label for="display"><span>Отображать на сайте</span></label>
                    </div>
                @endcan

                {{-- Чекбокс модерации --}}
                @can ('moderator', $album)
                    @if ($album->moderation == 1)
                    <div class="small-12 cell checkbox">
                        @include('includes.inputs.moderation', ['value'=>$album->moderation, 'name'=>'moderation'])
                    </div>
                    @endif
                @endcan

                {{-- Чекбокс системной записи --}}
                @can ('god', $album)
                    <div class="small-12 cell checkbox">
                        @include('includes.inputs.system', ['value'=>$album->system_item, 'name'=>'system_item']) 
                    </div>
                @endcan   

                <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
                </div>
            </div> {{-- Конец первого таба --}}

             {{-- Второй таб --}}
            <div class="tabs-panel" id="content-panel-2">
                 {{-- Контейнер для разграничения --}}
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">
                        <fieldset class="fieldset-access">
                            <legend>Принимать к загрузке в следующих размерах:</legend>
                                @php 
                                    $album_settings_default = config()->get('settings'); 

                                @endphp
                                <div class="grid-x grid-padding-x">
                                    <div class="small-12 medium-6 cell">
                                        <label>Ширина
                                            @include('includes.inputs.digit', ['value'=>$album_settings->img_min_width, 'name'=>'img_min_width', 'required'=>'', 'placeholder'=>$album_settings_default['img_min_width'], 'pattern' => '[0-9\W\s]{0,4}'])
                                        </label>
                                    </div>
                                    <div class="small-12 medium-6 cell">
                                        <label>Высота
                                            @include('includes.inputs.digit', ['value'=>$album_settings->img_min_height, 'name'=>'img_min_height', 'required'=>'', 'placeholder'=>$album_settings_default['img_min_height'], 'pattern' => '[0-9\W\s]{0,4}'])
                                        </label>
                                    </div>

                                  <div class="small-12 cell radiobutton">
                                    {{ Form::radio('upload_mode', 0, true, ['id'=>'mode_min']) }}
                                    <label for="mode_min"><span>Указаны минимальные размеры</span></label>

                                    {{ Form::radio('upload_mode', 1, false, ['id'=>'mode_fix']) }}
                                    <label for="mode_fix"><span>Загружать в строго указанных размерах</span></label>
                                  </div>


                                </div>

                        </fieldset>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <fieldset class="fieldset-access">
                            <legend>Форматы сохранения изображений:</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Ширина маленького
                                        @include('includes.inputs.digit', ['value'=>$album_settings->img_small_width, 'name'=>'img_small_width', 'required'=>'', 'placeholder'=>$album_settings_default['img_small_width']])
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Высота маленького
                                        @include('includes.inputs.digit', ['value'=>$album_settings->img_small_height, 'name'=>'img_small_height', 'required'=>'', 'placeholder'=>$album_settings_default['img_small_height']])
                                    </label>
                                </div>
                            </div>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Ширина среднего
                                        @include('includes.inputs.digit', ['value'=>$album_settings->img_medium_width, 'name'=>'img_medium_width', 'required'=>'', 'placeholder'=>$album_settings_default['img_medium_width']])
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Высота среднего
                                        @include('includes.inputs.digit', ['value'=>$album_settings->img_medium_height, 'name'=>'img_medium_height', 'required'=>'', 'placeholder'=>$album_settings_default['img_medium_height']])
                                    </label>
                                </div>
                            </div>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Ширина большого
                                        @include('includes.inputs.digit', ['value'=>$album_settings->img_large_width, 'name'=>'img_large_width', 'required'=>'', 'placeholder'=>$album_settings_default['img_large_width']])
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Высота большого
                                        @include('includes.inputs.digit', ['value'=>$album_settings->img_large_height, 'name'=>'img_large_height', 'required'=>'', 'placeholder'=>$album_settings_default['img_large_height']])
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                    </div>
                </div>

            </div> {{-- Конец второго таба --}}
        </div> {{-- Конец контейнера табов --}}
    </div> {{-- Конец контейнера для разграничения --}}
