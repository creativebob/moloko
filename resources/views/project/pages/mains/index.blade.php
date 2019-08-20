@extends($site->alias.'.layouts.app')

@section('inhead')
@endsection

@section('header')
    {{-- Сайдбар услуг --}}
    @include($site->alias.'.layouts.headers.header')
@endsection

@section('nav')
    {{-- Навигация --}}
    @include($site->alias.'.layouts.navigations.nav', ['align' => 'left'])
@endsection

@section('content')
    {{-- Сайдбар услуг --}}
{{--    @include($site->alias.'.layouts.sidebars.sidebar_sticky')--}}

    {{-- Основой контент --}}
    @include($site->alias.'.pages.mains.main')
@endsection
`
@section('footer')
    {{-- Сайдбар услуг --}}
    @include($site->alias.'.layouts.footers.footer')
@endsection

@push('scripts')
@endpush
