@extends('project.layouts.app')

@section('title')
<title>Гаражные ворота | Воротная компания "Марс"</title>
<meta name="description" content="Контактные данные воротной компании Марс. Телефон в Иркутске: 8 (3952) 71-77-75">
@endsection

@section('content')
<div class="grid-x grid-padding-x">
	<div class="cell small-12 medium-12 large-12">

		<nav aria-label="You are here:" role="navigation">
			<ul class="breadcrumbs">
				<li><a href="/knowledge">База знаний</a></li>
				<li class="disabled">Гаражные ворота</li>

			</ul>
		</nav>
	</div>
</div>

<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">Гаражные ворота<span class="chapters"></span><a href="#anchor-menu" title="Наверх!"><span class="arrow-top"></span></a></h1>
	</div>

	<main class="cell small-12 medium-12 large-12 main-cont">
		<div class="featured-image-block-grid">
			<ul class="grid-x grid-margin-x large-up-4 small-up-2">
				<li class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/7LmyegQbUgU" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Обзор панелей секционных ворот</p>
				</li>
				<li class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/JBFLCMGe8D0" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Как работает автоматика зимой?</p>
				</li>
				<li class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/XcNzvTo7lmo" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Можно ли записать пульты самостоятельно?</p>
				</li>
				<li class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/8TgwlFNwxsI" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Что такое фотоэлементы?</p>
				</li>
			</ul>
		</div>
	</main>
</div>
@endsection