@extends('project.layouts.app')

@section('title')
<title>Доставка ворот в другой город | Воротная компания "Марс"</title>
<meta name="description" content="Контактные данные воротной компании Марс. Телефон в Иркутске: 8 (3952) 71-77-75">
@endsection

@section('content')
<div class="grid-x grid-padding-x">
	<div class="cell small-12 medium-12 large-12">
		<nav aria-label="You are here:" role="navigation">
			<ul class="breadcrumbs">
				<li><a href="/knowledge">База знаний</a></li>
				<li class="disabled">Ворота в другой город</li>
			</ul>
		</nav>
	</div>
</div>
<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">Доставка ворот в другой город<span class="chapters"></span>
			<a href="#anchor-menu" title="Наверх!">
				<span class="arrow-top"></span>
			</a>
		</h1>
	</div>
	<main class="cell small-12 medium-12 large-12 main-cont">

		<div class="grid-x">

			<iframe width="560" height="315" src="https://www.youtube.com/embed/_jx3y29VUdg?rel=0" frameborder="0" allowfullscreen></iframe>

			<div class="cell small-12 medium-12 large-12">

				<h3 class="head-reveal">Города в которые мы организовываем <br>доставку <span class="headaction">за свой счет</span>:</h3>
				<hr>
				<ul class="grid-x small-up-1 medium-up-2 large-up-3" data-equalizer data-equalize-by-grid-x="true">
					<li class="cell">Братск</li>
					<li class="cell">Зима</li>
					<li class="cell">Саянск</li>
					<li class="cell">Северобайкальск</li>
					<li class="cell">Иркутск <a href="/contacts" class="smalllink">- офис</a></li>
					<li class="cell">Красноярск</li>
					<li class="cell">Тыреть</li>
					<li class="cell">Улан-Удэ</li>
					<li class="cell">Усть-Илимск</li>
					<li class="cell">Усть-Кут</li>
					<li class="cell">Черемхово</li>
					<li class="cell">Чита</li>
				</ul>
				<hr style="padding-top: 1.5rem;">
				<br>
				<p>Если вашего города нет в списке - просто напишите нам.<br>Мы рассмотрим возможность бесплатной доставки в ваш город и перезвоним в короткие сроки.</p>

				<div class="cell small-12 medium-12 large-12 cls">
					@include('project.includes.forms.city')
				</div>

			</div>
		</div>
	</main>
</div>
@endsection