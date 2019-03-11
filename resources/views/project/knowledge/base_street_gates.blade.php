@extends('project.layouts.app')

@section('title')
<title>Уличные ворота | Воротная компания "Марс"</title>
<meta name="description" content="Контактные данные воротной компании Марс. Телефон в Иркутске: 8 (3952) 71-77-75">
@endsection

@section('content')
<div class="grid-x grid-padding-x">
	<div class="cell small-12 medium-12 large-12">
		<nav aria-label="You are here:" role="navigation">
			<ul class="breadcrumbs">
				<li><a href="/knowledge">База знаний</a></li>
				<li class="disabled">Уличные ворота</li>
			</ul>
		</nav>
	</div>
</div>

<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">Уличные ворота<span class="chapters"></span><a href="#anchor-menu" title="Наверх!" id="uper"><span class="arrow-top"></span></a></h1>
	</div>
	<main class="cell small-12 medium-12 large-12 main-cont">
		<div class="featured-image-block-grid">
			<div class="grid-x large-up-4 small-up-2">
				<div class="featured-image-block cell">
					<a href="#">
						<iframe width="560" height="315" src="https://www.youtube.com/embed/OwLrpfrrwsw" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">На что следует обратить внимание при выборе воротной компании?</p>
				</div>
			</div>
		</div>
	</main>
</div>
@endsection