@extends('layouts.app')

@section('title', 'Редактировать кейс')

@section('breadcrumbs', Breadcrumbs::render('portfolio-section-edit', $portfolio, $pageInfo, $business_case))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ кейс "{{ $business_case->name }}"</h2>
        </div>
        <div class="top-bar-right"></div>
    </div>
@endsection

@section('content')
    {{ Form::model($business_case, ['route' => ['business_cases.update', 'portfolio_id' => $portfolio->id, $business_case->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
        @method('PATCH')
        @include('system.pages.business_cases.form', ['submit_text' => 'Редактировать'])
    {{ Form::close() }}
@endsection
