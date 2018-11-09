@section('inhead')
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@extends('layouts.app')

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content')
    <div class="container">
        <div class="row">

                    <div class="grid-x grid-padding-x">

                        @if(!empty($widgets))
                            @foreach($widgets as $name_widget => $widget)

                            <div class="small-12 medium-12 cell">
                                <div class="card">
                                    <div class="card-section">
                                        <div class="grid-x grid-padding-x">
                                            <div class="auto cell"><h3 class="widget-h3">{{ $widget['widget_info']->name }}</h3></div>
                                            <div class="shrink cell">
                                                <div class="sprite icon-drop"></div>
                                            </div>
                                        </div>
                                    </div>

                                    @include('includes.widgets.'.$name_widget, ['widget'=>$widget['data']])

                                </div>
                            </div>


                            @endforeach
                        @endif

                    </div>

        </div>
    </div>
@endsection

@section('scripts')

{{-- Скрипт сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')

@endsection