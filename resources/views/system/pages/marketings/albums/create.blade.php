@extends('layouts.app')

@section('title', 'Новый альбом')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">СОЗДАНИЕ НОВОГО АЛЬБОМА</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::open(['route' => 'albums.store', 'data-abide', 'novalidate']) !!}
    @include('system.pages.marketings.albums.form', ['submitText' => 'Добавить'])
    {!! Form::close() !!}
@endsection



