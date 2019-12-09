@extends('layouts.app')

@section('title', 'Редактировать страницу')

@section('breadcrumbs', Breadcrumbs::render('site-section-edit', $site, $page_info, $page))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ страницу "{{ $page->name }}"</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($page, ['url' => '/admin/sites/' . $site_id . '/pages/' . $page->id, 'data-abide', 'novalidate', 'files' => 'true']) }}
{{ method_field('PATCH') }}

@include('pages.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('pages.scripts')
@include('includes.scripts.upload-file')

@include('includes.scripts.ckeditor')
@endsection


