<div class="grid-x grid-padding-x">
    <div class="cell small-12 medium-6">
        <div class="grid-x grid-padding-x">
            <div class="small-12 cell">
                <label>Название страницы (Title)
                    @include('includes.inputs.varchar', ['name' => 'title'])
                </label>
            </div>
            <div class="small-12 cell">
                <label>Заголовок страницы (H1)
                    @include('includes.inputs.varchar', ['name' => 'header'])
                </label>
            </div>
            <div class="small-12 cell">
                <label>Описание для вывода на сайт:
                    {{ Form::textarea('description', $catalogsServicesItem->description, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                </label>
            </div>
            <div class="small-12 cell">
                <label>Описание для поисковых систем (Description)
                    @include('includes.inputs.textarea', ['value' => $catalogsServicesItem->seo_description, 'name' => 'seo_description'])
                </label>
            </div>
            <div class="small-12 cell">
                <label>Список ключевых слов (Keywords)
                    @include('includes.inputs.varchar', ['name' => 'keywords'])
                </label>
            </div>
            <div class="small-12 cell">
                <label>Режим отображения
                    @include('includes.selects.display_modes')
                </label>
            </div>
            {!! Form::hidden('is_controllable_mode', 0) !!}
            <div class="small-12 cell checkbox">
                {!! Form::checkbox('is_controllable_mode', 1, $catalogsServicesItem->is_controllable_mode, ['id' => 'checkbox-is_controllable_mode']) !!}
                <label
                    for="checkbox-is_controllable_mode"><span>Разрешить смену отображения</span></label>
            </div>
            <div class="small-12 cell">
                <label>Выводить меру в качестве основной:
                    @include('includes.selects.directive_categories', ['item' => $catalogsServicesItem])
                </label>
            </div>
            {!! Form::hidden('is_show_subcategory', 0) !!}
            <div class="small-12 cell checkbox">
                {!! Form::checkbox('is_show_subcategory', 1, $catalogsServicesItem->is_show_subcategory, ['id' => 'checkbox-is_show_subcategory']) !!}
                <label
                    for="checkbox-is_show_subcategory"><span>Отображать ВСЕ для субкатегорий</span></label>
            </div>

            {!! Form::hidden('is_hide_submenu', 0) !!}
            <div class="small-12 cell checkbox">
                {!! Form::checkbox('is_hide_submenu', 1, $catalogsServicesItem->is_hide_submenu, ['id' => 'checkbox-is_hide_submenu']) !!}
                <label for="checkbox-is_hide_submenu"><span>Не отображать субменю</span></label>
            </div>
        </div>
    </div>
    <div class="cell small-12 medium-6">
        <label>Выберите аватар
            {{ Form::file('photo') }}
        </label>
        <div class="text-center">
            <img id="photo" src="{{ getPhotoPath($catalogsServicesItem) }}">
        </div>

        <div class="small-6 medium-6 cell">
            <label>Цвет для оформления
                {!! Form::text('color') !!}
            </label>
        </div>

        <label>Видео
            {{ Form::text('video_url', $catalogsServicesItem->video_url, []) }}
        </label>

        <label>Блок видео
            @include('includes.inputs.textarea', ['name' => 'video'])
        </label>
    </div>
</div>
