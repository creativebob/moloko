<div class="grid-x grid-padding-x">
    <div class="cell small-12 medium-6">

        <fieldset class="fieldset-access">
            <legend>Артикул</legend>

            <div class="grid-x grid-margin-x">
                <div class="small-12 medium-4 cell">
                    <label id="loading">Удобный (вручную)
                        {{ Form::text('manually', null, ['class' => 'check-field']) }}
                        <div class="sprite-input-right find-status"></div>
                        <div class="item-error">Такой артикул уже существует!</div>
                    </label>
                </div>

                <div class="small-12 medium-4 cell">
                    <label>Внешний
                        {{ Form::text('external') }}
                    </label>
                </div>

                <div class="small-12 medium-4 cell">
                    <label>Программный</label>
                    {{ Form::text('internal', null, ['disabled']) }}
                </div>
            </div>
        </fieldset>

        <fieldset class="fieldset-access">
            <legend>Умолчания для стоимости</legend>

            <div class="grid-x grid-margin-x">
                <div class="small-12 medium-6 cell">
                    <label>Себестоимость
                        <digit-component
                            name="cost_default"
                            :value="{{ $process->cost_default }}"
                        ></digit-component>
                    </label>
                </div>
                {{--                                <div class="small-12 medium-6 cell">--}}
                {{--                                    <label>Цена за (<span id="unit">{{ ($article->package_status == false) ? $article->group->unit->abbreviation : 'порцию' }}</span>)--}}
                {{--                                        {{ Form::number('price_default', null) }}--}}
                {{--                                    </label>--}}
                {{--                                </div>--}}
            </div>
        </fieldset>

        {{--                        @if(isset($raw))--}}
        {{--                            <fieldset class="fieldset-access">--}}
        {{--                                <legend>Умолчания для сырья</legend>--}}

        {{--                                <div class="grid-x grid-margin-x">--}}
        {{--                                    <div class="small-12 medium-6 cell">--}}
        {{--                                        <label>Еденица измерения--}}
        {{--                                            @include('products.articles.common.edit.select_units', [--}}
        {{--                                                'field_name' => 'unit_for_composition_id',--}}
        {{--                                                'units_category_id' => $article->unit->category_id,--}}
        {{--                                                'disabled' => null,--}}
        {{--                                                'data' => $raw->unit_for_composition_id ?? $raw->article->unit_id,--}}
        {{--                                            ])--}}
        {{--                                        </label>--}}
        {{--                                    </div>--}}
        {{--                                    <div class="small-12 medium-6 cell">--}}

        {{--                                    </div>--}}
        {{--                                </div>--}}
        {{--                            </fieldset>--}}
        {{--                        @endif--}}

        {{--                        <fieldset class="fieldset package-fieldset" id="package-fieldset">--}}

        {{--                            <legend class="checkbox">--}}
        {{--                                {!! Form::checkbox('package_status', 1, $article->package_status, ['id' => 'package', $disabled ? 'disabled' : '']) !!}--}}
        {{--                                <label for="package">--}}
        {{--                                    <span id="package-change">Сформировать порцию для приема на склад</span>--}}
        {{--                                </label>--}}
        {{--                            </legend>--}}

        {{--                            <div class="grid-x grid-margin-x" id="package-block">--}}
        {{--                                --}}{{-- <div class="small-12 cell @if ($article->package_status == null) package-hide @endif">--}}
        {{--                                    <label>Имя&nbsp;порции--}}
        {{--                                        {{ Form::text('package_name', $article->package_name, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}--}}
        {{--                                    </label>--}}
        {{--                                </div> --}}
        {{--                                <div class="small-6 cell @if (!$article->package_status) package-hide @endif">--}}
        {{--                                    <label>Сокр.&nbsp;имя--}}
        {{--                                        {{ Form::text('package_abbreviation',  $article->package_abbreviation, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}--}}
        {{--                                    </label>--}}
        {{--                                </div>--}}
        {{--                                <div class="small-6 cell @if (!$article->package_status) package-hide @endif">--}}
        {{--                                    <label>Кол-во,&nbsp;{{ $article->unit->abbreviation }}--}}
        {{--                                        {{ Form::text('package_count', $article->package_count, ['class'=>'digit-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}--}}
        {{--                                        <div class="sprite-input-right find-status" id="name-check"></div>--}}
        {{--                                        <span class="form-error">Введите количество</span>--}}
        {{--                                    </label>--}}
        {{--                                </div>--}}
        {{--                            </div>--}}
        {{--                        </fieldset>--}}

        @includeIf('products.processes.'.$item->getTable().'.fieldsets')

        <fieldset class="fieldset-access">
            <legend>Доступность</legend>

            {{-- Автоинициация --}}
            <div class="small-12 cell checkbox">
                {!! Form::hidden('is_auto_initiated', 0) !!}
                {!! Form::checkbox('is_auto_initiated', 1, $process->is_auto_initiated, ['id' => 'checkbox-is_auto_initiated']) !!}
                <label for="checkbox-is_auto_initiated"><span>Автоинициация</span></label>
            </div>

            {{-- Чекбокс архива --}}
            {!! Form::hidden('archive', 0) !!}
            @if ($item->archive == 1)

                <div class="small-12 cell checkbox">
                    {!! Form::checkbox('archive', 1, $item->archive, ['id' => 'checkbox-archive']) !!}
                    <label for="checkbox-archive"><span>Вывести из архива</span></label>
                </div>
            @endif

            {{-- Чекбокс черновика --}}
            {!! Form::hidden('draft', 0) !!}
            {{-- @if ($process->draft) --}}
            <div class="small-12 cell checkbox">
                {!! Form::checkbox('draft', 1, $process->draft, ['id' => 'checkbox-draft']) !!}
                <label for="checkbox-draft"><span>Черновик</span></label>
            </div>
            {{-- @endif --}}


            <div class="small-12 cell checkbox">
                {!! Form::hidden('serial', 0) !!}
                {!! Form::checkbox('serial', 1, $item->serial, ['id' => 'checkbox-serial']) !!}
                <label for="checkbox-serial"><span>Серийный номер</span></label>
            </div>

            {{-- Чекбоксы управления --}}
            @include('includes.control.checkboxes', ['item' => $item])
            <div class="small-12 cell ">
                <span id="composition-error" class="form-error"></span>
            </div>
        </fieldset>

        <fieldset class="fieldset-access">
            <legend>Дополнительное медиа</legend>
            <label>Видео
                {{ Form::text('video_url', $process->video_url, []) }}
            </label>
        </fieldset>

    </div>

    <div class="cell small-12 medium-6">
        <fieldset class="fieldset-access">
            <label>Слаг
                {{ Form::text('slug', $process->slug, []) }}
            </label>
        </fieldset>
    </div>
</div>
