@extends('layouts.app')

@section('title', 'Новое портфолио')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">ДОБАВЛЕНИЕ нового портфолио</h2>
        </div>
        <div class="top-bar-right"></div>
    </div>
@endsection

@section('content')
    {{ Form::open(['route' => 'portfolios.store', 'data-abide', 'novalidate', 'files'=>'true']) }}
        @include('system.pages.portfolios.form', ['submit_text' => 'Добавить'])
    {{ Form::close() }}
@endsection








