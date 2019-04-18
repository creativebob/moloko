@extends('project.layouts.app')

@section('title')
<title>Частые вопросы| Воротная компания "Марс"</title>
<meta name="description" content="Контактные данные воротной компании Марс. Телефон в Иркутске: 8 (3952) 71-77-75">
@endsection

@section('content')
<div class="grid-x grid-padding-x">
	<div class="cell small-12 medium-12 large-12">
		<nav aria-label="You are here:" role="navigation">
			<ul class="breadcrumbs">
				<li><a href="/knowledge">База знаний</a></li>
				<li class="disabled">Часто задаваемые вопросы</li>
			</ul>
		</nav>
	</div>
</div>

<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">Часто задаваемые вопросы<span class="chapters"></span><a href="#anchor-menu" title="Наверх!" id="uper"><span class="arrow-top"></span></a></h1>
	</div>
	<main class="cell small-12 medium-12 large-12 main-cont">
		<div class="featured-image-block-grid">
			<div class="grid-x grid-margin-x large-up-4 small-up-2">
				<div class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/cAvvnfr2j70?rel=0" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Важно ли из какого материала гаражный проем?</p>
				</div>

				<div class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/tffeOJd8jKc?rel=0" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Что такое дополнительный контур уплотнения?</p>
				</div>

				<div class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/ET8ZZ_fZePg?rel=0" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Важно ли, что потолок в гаража будет иметь боковой или фронтальный уклон?</p>
				</div>

				<div class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/BPWi__VJumE?rel=0" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Важно ли, что на потолке имеются элементы: навесные конструкции, балки, короба?</p>
				</div>

				<div class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/wpnCRpSM7WY?rel=0" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Что делать, если отсутствует дополнительный вход в гараж, кроме основного проема?</p>
				</div>

				<div class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/Wi3f3mgdncE?rel=0" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Возможно ли разблокировать автоматику изнутри?</p>
				</div>

				<div class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/rNAOGl6jH_g?rel=0" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Что такое притолока?</p>
				</div>

				<div class="featured-image-block cell">
					<a href="#">
						<iframe width="100%" src="https://www.youtube.com/embed/exunvbWZyhU?rel=0" frameborder="0" allowfullscreen></iframe>
					</a>
					<p class="text-center desk-know">Что такое пристенок?</p>
				</div>

			</div>
		</div>
	</main>
</div>

@endsection