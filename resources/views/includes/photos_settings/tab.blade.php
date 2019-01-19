{{-- Контейнер для разграничения --}}
<div class="grid-x grid-padding-x">
    <div class="small-12 medium-6 cell">

        @if ($item->has('photo_settings'))
        {!! Form::model($item->photo_settings, []) !!}
        @else
        {!! Form::open([]) !!}
        @endif

        <fieldset class="fieldset-access">

            <legend>Принимать к загрузке в следующих размерах:</legend>

            <div class="grid-x grid-padding-x">
                <div class="small-12 medium-6 cell">
                    <label>Ширина
                        @include('includes.inputs.digit', [
                            'name' => 'img_min_width',
                            'placeholder' => config('photo_settings')['img_min_width'],
                            'pattern' => '[0-9\W\s]{0,4}'
                        ]
                        )
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота
                        @include('includes.inputs.digit', [
                            'name' => 'img_min_height',
                            'placeholder' => config('photo_settings')['img_min_height'],
                            'pattern' => '[0-9\W\s]{0,4}'
                        ]
                        )
                    </label>
                </div>

                <div class="small-12 cell radiobutton">
                    {{ Form::radio('strict_mode', 0, config('photo_settings')['strict_mode'], ['id' => 'mode_min']) }}
                    <label for="mode_min"><span>Указаны минимальные размеры</span></label>

                    {{ Form::radio('strict_mode', 1, config('photo_settings')['strict_mode'], ['id' => 'mode_fix']) }}
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
                        @include('includes.inputs.digit', [
                            'name' => 'img_small_width',
                            'placeholder' => config('photo_settings')['img_small_width']
                        ]
                        )
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота маленького
                        @include('includes.inputs.digit', [
                            'name' => 'img_small_height',
                            'placeholder' => config('photo_settings')['img_small_height']
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
                            'placeholder' => config('photo_settings')['img_medium_width']
                        ]
                        )
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота среднего
                        @include('includes.inputs.digit', [
                            'name' => 'img_medium_height',
                            'placeholder' => config('photo_settings')['img_medium_height']
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
                            'placeholder' => config('photo_settings')['img_large_width']
                        ]
                        )
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Высота большого
                        @include('includes.inputs.digit', [
                            'name' => 'img_large_height',
                            'placeholder' => config('photo_settings')['img_large_height']
                        ]
                        )
                    </label>
                </div>
            </div>
        </fieldset>
        {!! Form::close() !!}

    </div>
</div>
        {{-- Настройки фотографий --}}