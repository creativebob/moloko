<!DOCTYPE html>
<html class="no-js" lang="ru" dir="ltr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<meta name="theme-color" content="#ffffff">
		<link rel="icon" href="favicon.svg">
		<link rel="mask-icon" href="mask-icon.svg" color="#007bff">
		<link rel="apple-touch-icon" href="apple-touch-icon.png">
		<link rel="manifest" href="/manifest.json">
		<link rel="shortcut icon" href="{{ asset('/favicon.ico') }}" type="image/x-icon">

		<link rel="canonical" href="{{ request()->url() }}"/>

		<link rel="stylesheet" href="{{ mix('/css/mwtour/app.min.css') }}">

		{{-- CSRF Token --}}
		<meta name="csrf-token" content="{{ csrf_token() }}">

		{{-- Дополнительные плагины / скрипты / стили для конкретной страницы --}}
		@yield('inhead')
		
		@if(config('app.env') == 'production')
			@include('project.composers.plugins.list')
		@endif
	</head>
	<body>
		<div id="app">
			<div class="wrap-header">
				<div class="grid-container">
					@yield('header')
				</div>
			</div>

			<div class="wrap-main">
				<div class="grid-container">
					@yield('content')
				</div>
			</div>

			<div class="wrap-footer">
				<div class="grid-container">
					@yield('footer')
				</div>
			</div>
		</div>

		<script src="{{ mix('/js/mwtour/app.js') }}"></script>
		<script>
			// Prevent small screen page refresh sticky bug
			$(window).on('sticky.zf.unstuckfrom:bottom', function(e) {
				if (!Foundation.MediaQuery.atLeast('medium')) {
					$(e.target).removeClass('is-anchored is-at-bottom').attr('style', '');
				}
			});
		</script>
		@stack('scripts')
	</body>
</html>
