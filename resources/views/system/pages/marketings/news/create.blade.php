@extends('layouts.app')

@section('title', 'Новая новость')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">ДОБАВЛЕНИЕ новости</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::open([
        'route' => 'news.store',
        'data-abide',
        'novalidate',
        'files' => 'true'
    ]
    ) !!}
    @include('system.pages.marketings.news.form', ['submitText' => 'Добавить'])
    {!! Form::close() !!}
@endsection
