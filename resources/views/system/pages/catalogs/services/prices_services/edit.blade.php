@extends('layouts.app')

@section('title', 'Редактирование прайса услуги')

@section('breadcrumbs', Breadcrumbs::render('prices_services-index', $catalogServices,  $pageInfo, $priceService))

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
                <a href="#tab-general" aria-selected="true">Общая информация</a>
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

            {!! Form::model($priceService, ['route' => ['prices_services.update', [$catalogId, $priceService->id]], 'data-abide', 'novalidate', 'files' => 'true']) !!}
            @method('PATCH')

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-general">
                @include('system.pages.catalogs.services.prices_services.tabs.general')
            </div>

            @can('index', App\Discount::class)
                <div class="tabs-panel" id="tab-discounts">
                    @include('system.common.discounts.discounts', ['item' => $priceService, 'entity' => 'prices_services'])
                </div>
            @endcan

            <div class="tabs-panel" id="tab-options">
                @include('system.pages.catalogs.services.prices_services.tabs.options')
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
