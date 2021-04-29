@extends('viandiesel.layouts.app')

@section('inhead')
	{{-- Вставка в head --}}
    @include('viandiesel.layouts.inheads.tmc', ['tmc' => $tool])
@endsection

@section('header')
    {{-- Сайдбар услуг --}}
    @include('viandiesel.layouts.headers.header')
@endsection

@section('content')

    {{-- Основой контент --}}
    @include('viandiesel.pages.equipment.main')
@endsection

@section('footer')
    {{-- Сайдбар услуг --}}
    @include('viandiesel.layouts.footers.footer')
@endsection
