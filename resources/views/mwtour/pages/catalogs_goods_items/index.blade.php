@extends('viandiesel.layouts.app')

@section('inhead')
	{{-- Вставка в head --}}
    @include('viandiesel.layouts.inheads.category', ['category' => $catalogs_goods_item])
@endsection

@section('header')
    {{-- Сайдбар услуг --}}
    @include('viandiesel.layouts.headers.header')
@endsection

@section('content')

    {{-- Основой контент --}}
    @include('viandiesel.pages.catalogs_goods_items.main')
@endsection

@section('footer')
    {{-- Сайдбар услуг --}}
    @include('viandiesel.layouts.footers.footer')
@endsection
