@extends('layouts.app')

@section('title', 'Новый кейс')

@section('breadcrumbs', Breadcrumbs::render('portfolio-section-create', $portfolio, $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ДОБАВЛЕНИЕ Нового кейса</h2>
    </div>
    <div class="top-bar-right"></div>
</div>
@endsection

@section('content')
    {{ Form::open(['route' => ['business_cases.store', $portfolio->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
        @include('system.pages.business_cases.form', ['submit_text' => 'Добавить'])
    {{ Form::close() }}
@endsection
