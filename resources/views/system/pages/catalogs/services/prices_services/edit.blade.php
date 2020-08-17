@extends('layouts.app')

@section('title', 'Редактирование прайса услуги')

@section('breadcrumbs', Breadcrumbs::render('prices_goods-index', $catalogServices,  $pageInfo, $priceService))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Редактирование прайса услуги &laquo{{ $priceService->service->process->name }}&raquo</h2>
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
                <a href="#tab-options" aria-selected="true">Общая информация</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {!! Form::model($priceService, ['route' => ['prices_services.update', 'catalog_id' => $catalogId, $priceService->id], 'data-abide', 'novalidate', 'files' => 'true']) !!}
            @method('PATCH')

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-options">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">
                        <div class="grid-x grid-padding-x">

                            <div class="cell small-12 medium-4">
                                <label>Цена
                                    {!! Form::number('price', $priceService->price, ['required']) !!}
                                </label>
                            </div>

                            <div class="cell small-12 medium-4">
                                <label>Внут. валюта
                                    {!! Form::number('points', $priceService->points, ['required']) !!}
                                </label>
                            </div>

                            <div class="cell small-12 medium-4">
                                <label>Индивидуальная скидка
                                    {!! Form::number('discount', $priceService->discount, ['required']) !!}
                                </label>
                            </div>


                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('status', 0) !!}
                                {!! Form::checkbox('status', 1, $priceService->status, ['id' => 'checkbox-status']) !!}
                                <label for="checkbox-status"><span>Продан</span></label>
                            </div>

                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('is_hit', 0) !!}
                                {!! Form::checkbox('is_hit', 1, $priceService->is_hit, ['id' => 'checkbox-is_hit']) !!}
                                <label for="checkbox-is_hit"><span>Хит</span></label>
                            </div>

                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('is_new', 0) !!}
                                {!! Form::checkbox('is_new', 1, $priceService->is_new, ['id' => 'checkbox-is_new']) !!}
                                <label for="checkbox-is_new"><span>Новинка</span></label>
                            </div>

                        </div>

                    </div>

                    @include('includes.control.checkboxes', ['item' => $priceService])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class' => 'button']) }}
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
