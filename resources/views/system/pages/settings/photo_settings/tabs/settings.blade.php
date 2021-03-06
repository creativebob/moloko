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
                        <digit-component
                            name="img_min_width"
                            value="{{ optional($photoSetting)->img_min_width }}"
                            :limit-min="1"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->img_min_width }}"
                            :required="true"
                        ></digit-component>
                        {{--                        @include('includes.inputs.digit', [--}}
                        {{--                            'name' => 'img_min_width',--}}
                        {{--                            'pattern' => '[0-9\W\s]{0,4}'--}}
                        {{--                        ]--}}
                        {{--                        )--}}
                    </label>
                </div>
                <div class="cell small-12 medium-6">
                    <label>Высота
                        <digit-component
                            name="img_min_height"
                            value="{{ optional($photoSetting)->img_min_height }}"
                            :limit-min="1"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->img_min_height }}"
                            :required="true"
                        ></digit-component>
                        {{--                        @include('includes.inputs.digit', [--}}
                        {{--                            'name' => 'img_min_height',--}}
                        {{--                            'pattern' => '[0-9\W\s]{0,4}'--}}
                        {{--                        ]--}}
                        {{--                        )--}}
                    </label>
                </div>

                {{--                <div class="cell small-12 radiobutton">--}}
                {{--                    {{ Form::radio('strict_mode', 0, isset($photoSetting->strict_mode) ? $photoSetting->strict_mode : true, ['id' => 'mode_min']) }}--}}
                {{--                    <label for="mode_min"><span>Указаны минимальные размеры</span></label>--}}

                {{--                    {{ Form::radio('strict_mode', 1, isset($photoSetting->strict_mode) ? $photoSetting->strict_mode : false, ['id' => 'mode_fix']) }}--}}
                {{--                    <label for="mode_fix"><span>Загружать в строго указанных размерах</span></label>--}}
                {{--                </div>--}}

                <div class="cell small-12 medium-6">
                    <label>Размеры
                        {!! Form::select('strict_mode', [0 => 'Указаны минимальные размеры', 1 => 'Загружать в строго указанных размерах'], null, ['required']) !!}
                    </label>
                </div>

                <div class="cell small-12 medium-6">
                    <label>Режим обрезки
                        {!! Form::select('crop_mode', [1 => 'Уменьшение по ширине', 2 => 'Уменьшение по высоте', 3 => 'Уменьшение по ширине, обрезка высоты', 4 => 'Уменьшение по высоте, обрезка ширины', 5 => 'Подгонка под пропорцию с отсечением лишнего'], null, ['required']) !!}
                    </label>
                </div>

                <div class="cell small-12 medium-6">
                    <label>Качество сжатия
                        <digit-component
                            name="quality"
                            value="{{ optional($photoSetting)->quality }}"
                            :limit-min="1"
                            :limit-max="100"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->quality }}"
                            :required="true"
                        ></digit-component>
                    </label>
                </div>

                <div class="cell small-12 medium-6">
                    <label>Размер изображения (кб)
                        <digit-component
                            name="img_max_size"
                            value="{{ optional($photoSetting)->img_max_size }}"
                            :limit-min="1"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->img_max_size }}"
                            :required="true"
                        ></digit-component>
                    </label>
                </div>

            </div>

        </fieldset>

        <fieldset class="fieldset-access">

            <legend>Форматы изображений:</legend>
            <div class="grid-x grid-padding-x">

                <div class="cell small-12 medium-6">
                    <label>Форматы
                        @include('includes.inputs.name', [
                            'name' => 'img_formats',
                            'required' => true,
                            'value' => isset($photoSetting->img_formats) ? $photoSetting->img_formats : 'jpg,JPG'
                        ]
                        )
                    </label>
                </div>

                <div class="cell small-12 medium-6">
                    <label>Формат изображния для записи
                        {!! Form::select('store_format', ['jpg' => 'jpg'], null, ['required']) !!}
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
                        <digit-component
                            name="img_small_width"
                            value="{{ optional($photoSetting)->img_small_width }}"
                            :limit-min="1"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->img_small_width }}"
                            :required="true"
                        ></digit-component>
                        {{--                        @include('includes.inputs.digit', [--}}
                        {{--                            'name' => 'img_small_width',--}}
                        {{--                        ]--}}
                        {{--                        )--}}
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота маленького
                        <digit-component
                            name="img_small_height"
                            value="{{ optional($photoSetting)->img_small_height }}"
                            :limit-min="1"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->img_small_height }}"
                            :required="true"
                        ></digit-component>
                        {{--                        @include('includes.inputs.digit', [--}}
                        {{--                            'name' => 'img_small_height',--}}
                        {{--                        ]--}}
                        {{--                        )--}}
                    </label>
                </div>
            </div>
            <div class="grid-x grid-padding-x">
                <div class="small-12 medium-6 cell">
                    <label>Ширина среднего
                        <digit-component
                            name="img_medium_width"
                            value="{{ optional($photoSetting)->img_medium_width }}"
                            :limit-min="1"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->img_medium_width }}"
                            :required="true"
                        ></digit-component>
                        {{--                        @include('includes.inputs.digit', [--}}
                        {{--                            'name' => 'img_medium_width',--}}
                        {{--                        ]--}}
                        {{--                        )--}}
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота среднего
                        <digit-component
                            name="img_medium_height"
                            value="{{ optional($photoSetting)->img_medium_height }}"
                            :limit-min="1"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->img_medium_height }}"
                            :required="true"
                        ></digit-component>
                        {{--                        @include('includes.inputs.digit', [--}}
                        {{--                            'name' => 'img_medium_height',--}}
                        {{--                        ]--}}
                        {{--                        )--}}
                    </label>
                </div>
            </div>
            <div class="grid-x grid-padding-x">
                <div class="small-12 medium-6 cell">
                    <label>Ширина большого
                        <digit-component
                            name="img_large_width"
                            value="{{ optional($photoSetting)->img_large_width }}"
                            :limit-min="1"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->img_large_width }}"
                            :required="true"
                        ></digit-component>
                        {{--                        @include('includes.inputs.digit', [--}}
                        {{--                            'name' => 'img_large_width',--}}
                        {{--                        ]--}}
                        {{--                        )--}}
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота большого
                        <digit-component
                            name="img_large_height"
                            value="{{ optional($photoSetting)->img_large_height }}"
                            :limit-min="1"
                            :decimal-place="0"
                            placeholder="{{ $defaultPhotoSettings->img_large_height }}"
                            :required="true"
                        ></digit-component>
                        {{--                        @include('includes.inputs.digit', [--}}
                        {{--                            'name' => 'img_large_height',--}}
                        {{--                        ]--}}
                        {{--                        )--}}
                    </label>
                </div>

            </div>
        </fieldset>
        {!! Form::close() !!}
    </div>
</div>
