@extends('layouts.app')

@section('title', 'Редактировать пользователя')

@section('breadcrumbs', Breadcrumbs::render('site-section-edit', $site, $pageInfo, $user))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ ПОЛЬЗОВАТЕЛЯ</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')

    {{ Form::model($user, ['route' => ['users.update', $site->id, $user->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
    {{ method_field('PATCH') }}
    @include('system.pages.marketings.users.form', ['submitButtonText' => 'Редактировать пользователя',])
    {{ Form::close() }}

@endsection

@section('modals')
    {{-- Модалка добавления роли --}}
    @include('includes.modals.modal-add-role')

    {{-- Модалка удаления с ajax --}}
    @include('includes.modals.modal-delete-ajax')
@endsection

@push('scripts')
    @include('system.pages.marketings.users.scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.delete-from-page-script')
    @include('includes.scripts.upload-file')
    @include('includes.scripts.extra-phone')
@endpush


