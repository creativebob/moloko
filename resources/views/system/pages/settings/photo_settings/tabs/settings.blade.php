{{-- Контейнер для разграничения --}}
<div class="grid-x grid-padding-x">
    <div class="small-12 medium-6 cell">

        @isset($photoSetting)
        {!! Form::model($photoSetting, []) !!}
        @else
        {!! Form::open([]) !!}
        @endisset

        <fieldset class="fieldset-access">

            <legend>Принимать к загрузке в следующих размерах:</legend>

            <div class="grid-x grid-padding-x">
                <div class="cell small-12 medium-6">
                    <label>Ширина
                        @include('includes.inputs.digit', [
                            'name' => 'img_min_width',
                            'pattern' => '[0-9\W\s]{0,4}'
                        ]
                        )
                    </label>
                </div>
                <div class="cell small-12 medium-6">
                    <label>Высота
                        @include('includes.inputs.digit', [
                            'name' => 'img_min_height',
                            'pattern' => '[0-9\W\s]{0,4}'
                        ]
                        )
                    </label>
                </div>

                <div class="cell small-12 radiobutton">
                    {{ Form::radio('strict_mode', 0, isset($photoSetting->strict_mode) ? $photoSetting->strict_mode : true, ['id' => 'mode_min']) }}
                    <label for="mode_min"><span>Указаны минимальные размеры</span></label>

                    {{ Form::radio('strict_mode', 1, isset($photoSetting->strict_mode) ? $photoSetting->strict_mode : false, ['id' => 'mode_fix']) }}
                    <label for="mode_fix"><span>Загружать в строго указанных размерах</span></label>
                </div>


                <div class="cell small-12 medium-6">
                    <label>Форматы
                        @include('includes.inputs.name', [
                            'name' => 'img_formats',
                        ]
                        )
                    </label>
                </div>

                <div class="cell small-12 medium-6">
                    <label>Формат изображния для записи
                        {!! Form::select('store_format', ['jpg' => 'jpg']) !!}
                    </label>
                </div>

                <div class="cell small-12 medium-6">
                    <label>Режим обрезки
                        {!! Form::select('crop_mode', [1 => 'Пропорциональное уменьшение', 2 => 'Пропорциональная обрезка']) !!}
                    </label>
                </div>

                <div class="cell small-12 medium-6">
                    <label>Качество сжатия
                        <digit-component
                            name="quality"
                            value="{{ isset($photoSetting->quality) ? $photoSetting->quality : 80 }}"
                            :limit-min="1"
                            :limit-max="100"
                            :decimal-place="0"
                        ></digit-component>
                    </label>
                </div>

                <div class="cell small-12 medium-6">
                    <label>Размер изображения (кб)
                        <digit-component
                            name="img_max_size"
                            value="{{ isset($photoSetting->img_max_size) ? $photoSetting->img_max_size : 12000 }}"
                            :limit-min="1"
                            :decimal-place="0"
                        ></digit-component>
                    </label>
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
                        @include('includes.inputs.digit', [
                            'name' => 'img_small_width',
                        ]
                        )
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота маленького
                        @include('includes.inputs.digit', [
                            'name' => 'img_small_height',
                        ]
                        )
                    </label>
                </div>
            </div>
            <div class="grid-x grid-padding-x">
                <div class="small-12 medium-6 cell">
                    <label>Ширина среднего
                        @include('includes.inputs.digit', [
                            'name' => 'img_medium_width',
                        ]
                        )
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота среднего
                        @include('includes.inputs.digit', [
                            'name' => 'img_medium_height',
                        ]
                        )
                    </label>
                </div>
            </div>
            <div class="grid-x grid-padding-x">
                <div class="small-12 medium-6 cell">
                    <label>Ширина большого
                        @include('includes.inputs.digit', [
                            'name' => 'img_large_width',
                        ]
                        )
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота большого
                        @include('includes.inputs.digit', [
                            'name' => 'img_large_height',
                        ]
                        )
                    </label>
                </div>

            </div>
        </fieldset>
        {!! Form::close() !!}
    </div>
</div>
