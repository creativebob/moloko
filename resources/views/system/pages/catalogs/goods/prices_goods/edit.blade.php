@extends('layouts.app')

@section('title', 'Редактирование прайса товара')

@section('breadcrumbs', Breadcrumbs::render('prices_goods-index', $catalogGoods,  $pageInfo, $priceGoods))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Редактирование прайса товара &laquo{{ $priceGoods->goods->article->name }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-main" aria-selected="true">Общая информация</a>
            </li>

            @can('index', App\Discount::class)
            <li class="tabs-title">
                <a href="#tab-discounts" data-tabs-target="tab-discounts">Скидки</a>
            </li>
            @endcan

            <li class="tabs-title">
                <a href="#tab-options" aria-selected="true">Опции</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {!! Form::model($priceGoods, ['route' => ['prices_goods.update', [$catalogId, $priceGoods->id]], 'data-abide', 'novalidate', 'files' => 'true']) !!}
            @method('PATCH')

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-main">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">
{{--                        <prices-goods-discount-component--}}
{{--                            :item="{{ $priceGoods }}"--}}
{{--                        ></prices-goods-discount-component>--}}
                        <div class="grid-x grid-padding-x">

                            <div class="cell small-12 medium-4">
                                <label>Цена
                                    <digit-component
                                        name="price"
                                        :value="{{ $priceGoods->price }}"
                                        :required="true"
                                    ></digit-component>
                                    {{--                                    {!! Form::number('percent', null, ['disabled' => $disabled]) !!}--}}
                                </label>
                            </div>

                            <div class="cell small-12 medium-4">
                                <label>Внут. валюта
                                    <digit-component
                                        name="points"
                                        :value="{{ $priceGoods->points }}"
                                        :decimal-place="0"
                                    ></digit-component>
{{--                                    {!! Form::number('points', $priceGoods->points, ['required']) !!}--}}
                                </label>
                            </div>



{{--                            <div class="cell small-12 medium-4">--}}
{{--                                <label>Тип скидки--}}
{{--                                    {!! Form::select('discount_mode', [1 => 'Проценты', 2 => 'Валюта'], $priceGoods->discount_mode, ['required']) !!}--}}
{{--                                </label>--}}
{{--                            </div>--}}

                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('is_show_price', 0) !!}
                                {!! Form::checkbox('is_show_price', 1, $priceGoods->is_show_price, ['id' => 'checkbox-is_show_price']) !!}
                                <label for="checkbox-is_show_price"><span>Показывать старую цену</span></label>
                            </div>


                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('status', 0) !!}
                                {!! Form::checkbox('status', 1, $priceGoods->status, ['id' => 'checkbox-status']) !!}
                                <label for="checkbox-status"><span>Продан</span></label>
                            </div>

                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('is_hit', 0) !!}
                                {!! Form::checkbox('is_hit', 1, $priceGoods->is_hit, ['id' => 'checkbox-is_hit']) !!}
                                <label for="checkbox-is_hit"><span>Хит</span></label>
                            </div>

                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('is_new', 0) !!}
                                {!! Form::checkbox('is_new', 1, $priceGoods->is_new, ['id' => 'checkbox-is_new']) !!}
                                <label for="checkbox-is_new"><span>Новинка</span></label>
                            </div>

                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('is_preorder', 0) !!}
                                {!! Form::checkbox('is_preorder', 1, $priceGoods->is_preorder, ['id' => 'checkbox-is_preorder']) !!}
                                <label for="checkbox-is_preorder"><span>Продажа по предзаказу</span></label>
                            </div>

                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('is_priority', 0) !!}
                                {!! Form::checkbox('is_priority', 1, $priceGoods->is_priority, ['id' => 'checkbox-is_priority']) !!}
                                <label for="checkbox-is_priority"><span>Приоритет продажи для менеджеров</span></label>
                            </div>

                        </div>

                    </div>

                    @include('includes.control.checkboxes', ['item' => $priceGoods])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class' => 'button']) }}
                    </div>
                </div>
            </div>

            @can('index', App\Discount::class)
                <div class="tabs-panel" id="tab-discounts">
                    @include('system.common.discounts.discounts', ['item' => $priceGoods, 'entity' => 'prices_goods'])
                </div>
            @endcan

            <div class="tabs-panel" id="tab-options">
                <div class="grid-x grid-padding-x">
                    <div class="cell small-12 medium-3">
                        <label>Альтернативное название:
                            {!! Form::text('name_alt', $priceGoods->name_alt) !!}
                        </label>
                    </div>

                    <div class="cell small-12 medium-2">
                        <label>Внешний ID:
                            {!! Form::text('external', $priceGoods->external) !!}
                        </label>
                    </div>

                    <div class="small-12 cell checkbox">
                        {!! Form::hidden('is_exported_to_market', 0) !!}
                        {!! Form::checkbox('is_exported_to_market', 1, $priceGoods->is_exported_to_market, ['id' => 'checkbox-is_exported_to_market']) !!}
                        <label for="checkbox-is_exported_to_market"><span>Разрешить экспорт товара на Яндекс Маркет</span></label>
                    </div>

                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
