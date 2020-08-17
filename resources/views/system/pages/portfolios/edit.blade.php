@extends('layouts.app')

@section('title', 'Редактировать портфолио')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $pageInfo, $portfolio))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ портфолио</h2>
        </div>
        <div class="top-bar-right"></div>
    </div>
@endsection

@section('content')
    {{ Form::model($portfolio, ['route' => ['portfolios.update', $portfolio->id], 'data-abide', 'novalidate', 'files'=>'true']) }}
        @method('PATCH')
        @include('system.pages.portfolios.form', ['submit_text' => 'Редактировать'])
    {{ Form::close() }}
@endsection

