@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">Смена</h2>
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
                    <a href="#tab-general" aria-selected="true">Информация</a>
                </li>
            </ul>
        </div>
    </div>

    <div data-tabs-content="tabs" class="inputs tabs-margin-top">

        {{-- Первый таб --}}
        <div class="tabs-panel is-active" id="tab-general">
            @include('system.pages.shifts.shift.tabs.general')
        </div>
    </div>

@endsection
