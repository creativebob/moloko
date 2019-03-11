@extends('layouts.app')

@section('title')
<title>{{ $content[0]['title'] }}</title>
<meta name="description" content="{{ $content[0]['title'] }}">
@endsection

@section('content')
<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		@if (empty($content))
		<h1>Увы и ах (</h1>
		@else
		<!-- <h1>Плюс <span class="headaction-orange">1 год</span> гарантии на секционные ворота<a href="#anchor-menu" title="Наверх!"><span class="arrow-top"></span></a></h1> -->
		<h1>{{ $content[0]['title'] }}</h1>
		@endif
	</div>
	@if (!empty($content))
	<main class="cell small-12 medium-12 large-12 news solution">
		<div class="grid-x">
			<div class="cell small-12 bl-left">
				@php echo $content[0]['content'] @endphp
			</div>
		</div>
		<section id="callme" class="cell small-12 cont-section" data-magellan-target="callme">
			<h2>Закажите звонок!</h2>
			<p class="extra-head">Мы перезвоним и проконсультируем по предложению.</p>
			<div class="grid-x align-center">
				<div class="cell small-12 medium-12 large-8 cls">
					@include('includes.forms.call', ['remark' => 'Мне интересна акция: "'.$content[0]['title'].'"! Хочу узнать подробности!'])

				</div>
			</div>
		</section>
	</main>
	@endif
</div>
@endsection