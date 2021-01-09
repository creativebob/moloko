<div class="grid-x grid-padding-x">

    {{-- Левый блок на первой вкладке --}}
    <div class="small-12 large-6 cell">

        {{-- Основная инфа --}}
        <div class="grid-x grid-margin-x">
            <div class="small-12 medium-6 cell">

                <label>Название
                    {{ Form::text('name', $process->name, ['required']) }}
                </label>

                <processes-categories-with-groups-component
                    :item="{{ $item }}"
                    :process="{{ $process }}"
                    :categories='@json($categories_tree)'
                    :groups='@json($groups)'
                ></processes-categories-with-groups-component>

                <label>Производитель
                    @if ($item->category->manufacturers->isNotEmpty())
                        {!! Form::select('manufacturer_id', $item->category->manufacturers->pluck('company.name', 'id'), $process->manufacturer_id, [$disabled ? 'disabled' : '']) !!}
                    @else
                        @include('includes.selects.manufacturers', ['manufacturer_id' => $process->manufacturer_id, 'item' => $item])
                    @endif
                </label>

                <div class="grid-x grid-margin-x">
                    <div class="small-12 medium-6 cell">
                        <label>Единица измерения
                            @include('products.processes.common.edit.select_units', [
                                'units_category_id' => $process->unit->category_id,
                                'disabled' => null,
                                'value' => $process->unit_id,
                            ])
                        </label>
                    </div>
                    {{-- <div class="small-12 medium-6 cell">
                        @isset ($process->unit_id)
                            @if($process->group->units_category_id != 2)
                                <label>Вес единицы, {{ $process->weight_unit->abbreviation }}
                                    {!! Form::number('weight', null, ['disabled' => ($process->draft == 1) ? null : true]) !!}
                                </label>
                            @else
                                {{ Form::hidden('weight', $process->weight) }}
                            @endif
                        @endisset
                    </div> --}}
                </div>

                <label>Тип процесса
                    @include('includes.selects.processes_types', ['processes_type_id' => $process->processes_type_id])
                </label>

                {{-- Если указана ед. измерения - ШТ. --}}
                {{-- @if($item->getTable() == 'goods') --}}
                {{--                                @if($article->group->units_category_id == 6)--}}
                {{--                                    <div class="cell small-12 block-price-unit">--}}
                {{--                                        <fieldset class="minimal-fieldset">--}}
                {{--                                            <legend>Единица для определения цены</legend>--}}
                {{--                                            <div class="grid-x grid-margin-x">--}}
                {{--                                                <div class="small-12 medium-6 cell">--}}
                {{--                                                    @include('includes.selects.units_categories', [--}}
                {{--                                                        'default' => 6,--}}
                {{--                                                        'data' => $item->price_unit_category_id,--}}
                {{--                                                        'type' => 'article',--}}
                {{--                                                        'name' => 'price_unit_category_id',--}}
                {{--                                                        'id' => 'select-price-units_categories',--}}
                {{--                                                    ])--}}
                {{--                                                </div>--}}
                {{--                                                <div class="small-12 medium-6 cell">--}}
                {{--                                                    @include('includes.selects.units', [--}}
                {{--                                                        'default' => 32,--}}
                {{--                                                        'data' => $item->price_unit_id,--}}
                {{--                                                        'units_category_id' => $item->price_unit_category_id,--}}
                {{--                                                        'name' => 'price_unit_id',--}}
                {{--                                                        'id' => 'select-price-units',--}}
                {{--                                                    ])--}}
                {{--                                                </div>--}}
                {{--                                            </div>--}}
                {{--                                        </fieldset>--}}
                {{--                                    </div>--}}
                {{--                                @endif--}}
                {{--                                --}}{{-- @endif --}}
                {!! Form::hidden('id', null, ['id' => 'item-id']) !!}

            </div>

            <div class="small-12 medium-6 cell">
                <div class="small-12 cell">
                    <label>Фотография
                        {{ Form::file('photo') }}
                    </label>
                    <div class="text-center wrap-article-photo">
                        <img id="photo" src="{{ getPhotoPathPlugEntity($item) }}">
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- Конец левого блока на первой вкладке --}}

    {{-- Правый блок на первой вкладке --}}
    <div class="small-12 large-6 cell">

        <div class="grid-x">
            <div class="small-12 cell">
                <label>Описание
                    @include('includes.inputs.textarea', ['name' => 'description', 'value' => $process->description])
                </label>
            </div>
            @if($process->unit->category_id != 3)
                <div class="cell small-12">
                    <div class="grid-x grid-margin-x">
                        <div class="small-12 medium-3 cell">
                            <label>Продолжительность
                                {!! Form::number('length', $process->lengthTrans) !!}
                            </label>
                        </div>
                        <div class="small-12 medium-3 cell">
                            <label>Единица измерения
                                @include('products.processes.common.edit.select_units', [
                                   'name' => 'unit_length_id',
                                   'units_category_id' => 3,
                                   'value' => $process->unit_length_id,
                                   'disabled' => null,
                               ])
                            </label>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Метрики --}}
        @includeIf('products.processes.'.$item->getTable().'.metrics.metrics')
        @include('products.common.edit.metrics.metrics')


        <div id="item-inputs"></div>
        <div class="small-12 cell tabs-margin-top text-center">
            <div class="item-error" id="item-error">Такой артикул уже существует!<br>Измените значения!</div>
        </div>
    </div>
    {{-- Конец правого блока на первой вкладке --}}

    {{-- Кнопка --}}
    <div class="small-12 cell tabs-button tabs-margin-top">
        {{ Form::submit('Редактировать', ['class' => 'button', 'id' => 'add-item']) }}
    </div>

</div>
