@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content')
{{--    <div class="container">--}}
{{--        <div class="row">--}}

            <div class="grid-x grid-padding-x">

                @foreach($widgets as $widget)

                    <div class="small-12 medium-12 cell">
                        <div class="card">
                            <div class="card-section">
                                <div class="grid-x grid-padding-x">
                                    <div class="auto cell">
                                        <h3 class="widget-h3">{{ $widget->name }}</h3>
                                    </div>
                                    <div class="shrink cell">
                                        <div class="sprite icon-drop"></div>
                                    </div>
                                </div>
                            </div>

                            @include("system.pages.dashboard.widgets.{$widget->tag}")

                        </div>
                    </div>


                @endforeach

            </div>

{{--        </div>--}}
{{--    </div>--}}
@endsection

@push('scripts')
    {{-- Скрипт сортировки и перетаскивания для таблицы --}}
    {{--@include('includes.scripts.tablesorter-script')--}}
    {{--@include('includes.scripts.sortable-table-script')--}}
@endpush
