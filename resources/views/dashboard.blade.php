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
                                @include('includes.widgets.'.$name_widget)
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