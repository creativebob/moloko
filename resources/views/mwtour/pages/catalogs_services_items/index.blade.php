@extends('viandiesel.layouts.app')

@section('inhead')
	{{-- Вставка в head --}}
    @include('project.layouts.inheads.inhead_with_additionals', ['item' => $catalogs_services_item])
@endsection

@section('header')
    {{-- Сайдбар услуг --}}
    @include('viandiesel.layouts.headers.header')
@endsection

@section('content')

    {{-- Основой контент --}}
    @include('viandiesel.pages.catalogs_services_items.main')
@endsection

@section('footer')
    {{-- Сайдбар услуг --}}
    @include('viandiesel.layouts.footers.footer')
@endsection
