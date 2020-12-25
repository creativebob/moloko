<div class="grid-x grid-padding-x">

    {{-- Левый блок на первой вкладке --}}
    <div class="small-12 large-6 cell">

        {{-- Основная инфа --}}
        <div class="grid-x grid-margin-x">
            <div class="small-12 medium-6 cell">

                <label>Название
                    {{ Form::text('name', $article->name, ['required']) }}
                </label>

                <articles-categories-with-groups-component :item="{{ $item }}" :article="{{ $article }}"
                                                           :categories='@json($categories_tree)'
                                                           :groups='@json($groups)'></articles-categories-with-groups-component>

                <label>Производитель
                    @if ($item->category->manufacturers->isNotEmpty())
                        @if($item->getTable() == 'goods')
                            <manufacturers-component
                                :item='@json($item)'
                                :manufacturers='@json($item->category->manufacturers)'
                                disabled="{{ $disabled }}"
                            ></manufacturers-component>
                        @else
                            {!! Form::select('manufacturer_id', $item->category->manufacturers->pluck('company.name', 'id'), $article->manufacturer_id, [$disabled ? 'disabled' : '']) !!}
                        @endif
                    @else
                        @if($item->getTable() == 'goods')
                            @include('products.articles.common.edit.manufacturers')
                        @else
                            @include('includes.selects.manufacturers')
                        @endif
                    @endif
                </label>

                <div class="grid-x grid-margin-x">
                    <div class="small-12 medium-6 cell">
                        <label>Единица измерения
                            @include('products.articles.common.edit.select_units', [
                                'units_category_id' => $article->unit->category_id,
                                'disabled' => null,
                                'data' => $article->unit_id,
                            ])
                        </label>
                    </div>
                    {{-- <div class="small-12 medium-6 cell">
                        @isset ($article->unit_id)
                            @if($article->group->units_category_id != 2)
                                <label>Вес единицы, {{ $article->weight_unit->abbreviation }}
                                    {!! Form::number('weight', null, ['disabled' => ($article->draft == 1) ? null : true]) !!}
                                </label>
                            @else
                                {{ Form::hidden('weight', $article->weight) }}
                            @endif
                        @endisset
                    </div> --}}
                </div>


                {{-- Если указана ед. измерения - ШТ. --}}
                @if($item->getTable() == 'goods')
                    @if($article->group->units_category_id == 6)
                        <div class="cell small-12 block-price-unit">
                            <fieldset class="minimal-fieldset">
                                <legend>Единица для определения цены</legend>
                                <div class="grid-x grid-margin-x">
                                    <div class="small-12 medium-6 cell">
                                        @include('includes.selects.units_categories', [
                                            'default' => 6,
                                            'data' => $item->price_unit_category_id,
                                            'type' => 'article',
                                            'name' => 'price_unit_category_id',
                                            'id' => 'select-price-units_categories',
                                        ])
                                    </div>
                                    <div class="small-12 medium-6 cell">
                                        @include('includes.selects.units', [
                                            'default' => 32,
                                            'data' => $item->price_unit_id,
                                            'units_category_id' => $item->price_unit_category_id,
                                            'name' => 'price_unit_id',
                                            'id' => 'select-price-units',
                                        ])
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    @endif
                @endif
                {!! Form::hidden('id', null, ['id' => 'item-id']) !!}

                @if($item->getTable() == 'tools')
                    <div class="grid-x grid-margin-x">
                        <div class="cell small-12">
                            <label>Тип
                                @include('includes.selects.tools_types', ['value' => $item->tools_type_id, 'placeholder' => true])
                            </label>

                        </div>
                    </div>
                @endif

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
                    @include('includes.inputs.textarea', ['name' => 'description', 'value' => $article->description])
                </label>
            </div>
            @if($article->group->units_category_id != 2)
                <div class="cell small-12">
                    <div class="grid-x grid-margin-x">
                        <div class="small-12 medium-3 cell">
                            <label>Вес
                                {!! Form::number('weight', $article->weight_trans) !!}
                                {{-- ['disabled' => ($article->draft == 1) ? null : true]  --}}
                            </label>
                        </div>
                        <div class="small-12 medium-3 cell">
                            <label>Единица измерения
                                @include('products.articles.common.edit.select_units', [
                                    'field_name' => 'unit_weight_id',
                                    'units_category_id' => 2,
                                    'disabled' => null,
                                    'data' => $article->unit_weight_id ?? 7,
                                ])
                            </label>
                        </div>
                    </div>
                </div>
            @endif

            @if($article->group->units_category_id != 5)
                <div class="cell small-12">
                    <div class="grid-x grid-margin-x">
                        <div class="small-12 medium-3 cell">
                            <label>Объем
                                {!! Form::number('volume', $article->volume_trans) !!}
                            </label>
                        </div>
                        <div class="small-12 medium-3 cell">
                            <label>Единица измерения
                                @include('products.articles.common.edit.select_units', [
                                    'field_name' => 'unit_volume_id',
                                    'units_category_id' => 5,
                                    'disabled' => null,
                                    'data' => $article->unit_volume_id ?? 30,
                                ])
                            </label>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Метрики --}}
        @includeIf('products.articles.'.$item->getTable().'.metrics.metrics')

        @if ($item->getTable() == 'goods')
            @include('products.common.edit.metrics.metrics')
        @endif


        <div id="item-inputs"></div>
        <div class="small-12 cell tabs-margin-top text-center">
            <div class="item-error" id="item-error">Такой артикул уже существует!<br>Измените значения!</div>
        </div>
        {{ Form::hidden('item_id', $item->id) }}
    </div>
    {{-- Конец правого блока на первой вкладке --}}

    {{-- Кнопка --}}
    <div class="small-12 cell tabs-button tabs-margin-top">
        {{ Form::submit('Редактировать', ['class' => 'button', 'id' => 'add-item']) }}
    </div>

</div>
