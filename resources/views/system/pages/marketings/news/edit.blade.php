@extends('layouts.app')

@section('title', 'Редактировать новость')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $curNews->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАние новости "{{ $curNews->name }}"</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::model($curNews, [
        'route' => ['news.update', $curNews->id],
        'data-abide',
        'novalidate',
        'files' => 'true'
    ]
    ) !!}
    @method('PATCH')
    @include('system.pages.marketings.news.form', ['submitText' => 'Редактировать'])
    {!! Form::close() !!}
@endsection
