@extends('mwtour.layouts.app')

@section('inhead')
	{{-- Вставка в head --}}
    @include('mwtour.layouts.inheads.inhead')
    <link  href="/js/plugins/fancybox/dist/jquery.fancybox.min.css" rel="stylesheet">
@endsection

@section('header')
    {{-- Сайдбар услуг --}}
    @include('mwtour.layouts.headers.header')
@endsection

@section('content')
    {{-- Основой контент --}}
    @include('mwtour.pages.tour.main')
@endsection

@section('footer')
    {{-- Сайдбар услуг --}}
    @include('mwtour.layouts.footers.footer')
@endsection

@push('scripts')
    <script src="/js/plugins/fancybox/dist/jquery.fancybox.min.js"></script>
@endpush