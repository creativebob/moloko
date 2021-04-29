@extends('viandiesel.layouts.app')

@section('inhead')
	{{-- Вставка в head --}}
    @include('viandiesel.layouts.inheads.inhead')
    <link  href="/js/plugins/fancybox/dist/jquery.fancybox.min.css" rel="stylesheet">
@endsection

@section('header')
    {{-- Сайдбар услуг --}}
    @include('viandiesel.layouts.headers.header')
@endsection

@section('content')

    {{-- Основой контент --}}
    @include('viandiesel.pages.services.main')
@endsection

@section('footer')
    {{-- Сайдбар услуг --}}
    @include('viandiesel.layouts.footers.footer')
@endsection

@push('scripts')
    <script src="/js/plugins/fancybox/dist/jquery.fancybox.min.js"></script>
@endpush