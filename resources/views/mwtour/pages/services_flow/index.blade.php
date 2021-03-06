@extends('mwtour.layouts.app')

@section('inhead')
	{{-- Вставка в head --}}
    @include('mwtour.layouts.inheads.inhead')
    <link  href="/js/plugins/fancybox/dist/jquery.fancybox.min.css" rel="stylesheet">
@endsection

@section('header')
    {{-- Сайдбар услуг --}}
    @include('mwtour.layouts.headers.header-simple')
@endsection

@section('content')
    {{-- Основой контент --}}
    @include('mwtour.pages.services_flow.main')
@endsection

@section('footer')
    {{-- Сайдбар услуг --}}
    @include('mwtour.layouts.footers.footer')
@endsection

@push('scripts')
    <script src="/js/plugins/fancybox/dist/jquery.fancybox.min.js"></script>
    <script type="text/javascript">
        foundation.core.js;
        foundation.accordion.js;
    </script>
@endpush
