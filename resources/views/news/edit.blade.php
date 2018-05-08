@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать новость')

@section('breadcrumbs', Breadcrumbs::render('section-edit', $parent_page_info, $site, $page_info, $cur_news))

@section('title-content')
<div class="top-bar head-content">
  <div class="top-bar-left">
    <h2 class="header-content">РЕДАКТИРОВАние новости "{{ $cur_news->name }}"</h2>
 </div>
 <div class="top-bar-right">
 </div>
</div>
@endsection

@section('content')

{{ Form::model($cur_news, ['url' => '/sites/'.$site->alias.'/news/'.$cur_news->id, 'data-abide', 'novalidate', 'files'=>'true']) }}
{{ method_field('PATCH') }}

@include('news.form', ['submitButtonText' => 'Редактировать новость', 'param'=>''])

{{ Form::close() }}
@endsection

@section('modals')
{{-- Модалка добавления альбома --}}
@include('includes.modals.modal-add-album')
{{-- Модалка удаления с ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('news.scripts')
@include('includes.scripts.pickmeup-script')
@include('includes.scripts.upload-file')
@include('includes.scripts.delete-from-page-script')
@endsection


