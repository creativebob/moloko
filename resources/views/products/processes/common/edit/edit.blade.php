@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.dropzone-inhead')
    @include('includes.scripts.fancybox-inhead')
    @include('includes.scripts.sortable-inhead')

    @if ($entity == 'services')
        @include('includes.scripts.chosen-inhead')
    @endif
@endsection

@section('title', $title)

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $pageInfo, $process))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">{{ $title }} &laquo{{ $process->name }}&raquo</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@php
    $disabled = $process->draft == 0 ? true : null;
@endphp

@section('content')
    <div class="grid-x tabs-wrap">
        <div class="small-12 cell">
            <ul class="tabs-list" data-tabs id="tabs">

                <li class="tabs-title is-active">
                    <a href="#tab-general" aria-selected="true">Общая информация</a>
                </li>

                {{-- Табы для сущности --}}
                @includeIf($pageInfo->entity->view_path . '.tabs')

                <li class="tabs-title">
                    <a data-tabs-target="tab-photos" href="#tab-photos">Фотографии</a>
                </li>

                <li class="tabs-title">
                    <a data-tabs-target="tab-options" href="#tab-options">Опции</a>
                </li>

                @can('index', App\Site::class)
                    <li class="tabs-title">
                        <a data-tabs-target="tab-site" href="#tab-site">Настройка для сайта</a>
                    </li>
                @endcan

                <li class="tabs-title">
                    <a data-tabs-target="tab-positions" href="#tab-positions">Должности</a>
                </li>

                @if($process->processes_type_id == 2)
                    @can('index', App\Impact::class)
                        <li class="tabs-title">
                            <a data-tabs-target="tab-impacts" href="#tab-impacts">Обьекты воздействия</a>
                        </li>
                    @endcan
                @endif

            </ul>
        </div>
    </div>

    <div class="grid-x tabs-wrap inputs">
        <div class="small-12 cell tabs-margin-top">
            <div class="tabs-content" data-tabs-content="tabs">

                {{ Form::model($process, [
                    'route' => [$entity.'.update', $item->id],
                    'data-abide',
                    'novalidate',
                    'files' => 'true',
                    'id' => 'form-edit'
                ]
                ) }}
                @method('PATCH')

                {!! Form::hidden('previous_url', $previous_url ?? null) !!}

                {{-- Общая информация --}}
                <div class="tabs-panel is-active" id="tab-general">
                    @include('products.processes.common.edit.tabs.general')
                </div>

                {{-- Дополнительные опции --}}
                <div class="tabs-panel" id="tab-options">
                    @include('products.processes.common.edit.tabs.options')
                </div>

                {{-- Ценообразование --}}
                {{--                <div class="tabs-panel" id="price-rules">--}}
                {{--                    <div class="grid-x grid-padding-x">--}}
                {{--                        <div class="small-12 medium-6 cell">--}}

                {{--                            <fieldset class="fieldset-access">--}}
                {{--                                <legend>Базовые настройки</legend>--}}

                {{--                                <div class="grid-x grid-margin-x">--}}
                {{--                                    <div class="small-12 medium-6 cell">--}}
                {{--                                        <label>Себестоимость--}}
                {{--                                            {{ Form::number('cost_default', null) }}--}}
                {{--                                        </label>--}}
                {{--                                    </div>--}}
                {{--                                    <div class="small-12 medium-6 cell">--}}
                {{--                                        <label>Цена за (<span id="unit">{{ $process->unit->abbreviation }}</span>)--}}
                {{--                                            {{ Form::number('price_default', null) }}--}}
                {{--                                        </label>--}}
                {{--                                    </div>--}}
                {{--                                </div>--}}
                {{--                            </fieldset>--}}

                {{--                        </div>--}}
                {{--                    </div>--}}
                {{--                </div>--}}

                @includeIf($pageInfo->entity->view_path . '.tabs_content')

                {{-- Сайт --}}
                @can('index', App\Site::class)
                    <div class="tabs-panel" id="tab-site">
                        @include('products.processes.common.edit.tabs.site')
                    </div>
                @endcan

                @if($process->processes_type_id == 2)
                    @can('index', App\Impact::class)
                        <div class="tabs-panel" id="tab-impacts">
                            @include('products.processes.services.impacts.impacts')
                        </div>
                    @endcan
                @endif

                {{-- Должности --}}
                @can('index', App\Position::class)
                    <div class="tabs-panel" id="tab-positions">
                        @include('products.processes.common.edit.tabs.positions')
                    </div>
                @endcan

                {{ Form::close() }}

                {{-- Фотографии --}}
                <div class="tabs-panel" id="tab-photos">
                    @include('products.processes.common.edit.tabs.photos')
                </div>

            </div>
        </div>
    </div>
@endsection

@section('modals')
    @includeIf($pageInfo->entity->view_path . '.modals')
@endsection

@push('scripts')
    <script>

        // Основные настройки
        var category_entity = '{{ $category_entity }}';

    </script>

    @include('products.processes.common.edit.change_processes_groups_script')

    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.upload-file')
    @include('includes.scripts.ckeditor')

    @include('includes.scripts.dropzone', [
        'settings' => $settings,
        'item_id' => $process->id,
        'item_entity' => 'processes'
    ]
    )

    {{-- Проверка поля на существование --}}
    @include('includes.scripts.check', [
        'entity' => 'processes',
        'id' => $process->id
    ]
    )

    @includeIf($pageInfo->entity->view_path . '.scripts')
@endpush
