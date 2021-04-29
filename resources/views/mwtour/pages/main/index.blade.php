@extends('mwtour.layouts.app')

@section('inhead')
    {{-- Вставка в head --}}
    @include('mwtour.layouts.inheads.inhead')
@endsection

@section('header')
    {{-- Сайдбар услуг --}}
    @include('mwtour.layouts.headers.header')
@endsection

@section('nav')
    {{-- Навигация --}}
    @include('project.composers.navigations.navigation_by_align', ['align' => 'top'])
@endsection

@section('content')

    {{-- Основой контент --}}
    @include('mwtour.pages.main.main')
@endsection

@section('footer')
    {{-- Сайдбар услуг --}}
    @include('mwtour.layouts.footers.footer')
@endsection

@push('scripts')
@endpush
