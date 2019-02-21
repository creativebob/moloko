@extends('project.layouts.app')

@section('title')
<title>{{ $page->title }} | Воротная компания "Марс"</title>
<meta name="description" content="{{ $page->description }} ">
@endsection

@section('content')
<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">Контакты
			<span data-responsive-toggle="topAnchor" data-hide-for="medium">
				<span class="chapters" data-toggle></span>
			</span>
			<a href="#anchor-menu" title="Наверх!"><span class="arrow-top"></span></a>
		</h1>
	</div>
	<aside class="cell small-12 medium-3 large-3 left-sidebar" id="topAnchor" data-sticky-container>
		<nav data-sticky data-options="marginTop:8;" data-top-anchor="topAnchor" data-btm-anchor="callme:bottom">
			<ul class="menu vertical specific" data-magellan data-offset="110">
				<li><a href="#address">Наш адрес</a></li>
				<li><a href="#map">Мы на карте</a></li>
				<li><a href="#item3">График работы</a></li>
				<li><a href="#callme">Заказать звонок</a></li>
			</ul>
		</nav>
	</aside>

	<main class="cell small-12 medium-9 large-9 main-cont">
		<div class="grid-x sections">
			<section id="address" class="cell small-12 cont-section" data-magellan-target="address">
				<h2>Держите с нами связь,<br> а лучше <span class="redhead">приезжайте на кофе!</span></h2>
				<div class="grid-x">
					<div class="cell small-12 medium-12 large-6 contacts-block">
						@include('project.includes.partials.contacts_info')
					</div>
					<div class="cell small-12 medium-12 large-6">
						<img src="{{ asset('/project/img/coffee3.jpg') }}" alt="Кофейная кружка">
					</div>
				</div>
			</section>
			<section id="map" class="cell small-12 cont-section" data-magellan-target="map">
				<h2>Мы на карте</h2>
				<div class="grid-x">
					<div class="cell small-12 medium-12 large-12 large-centered">
						<div class="gis">
							<iframe frameborder="no" style="border: 1px solid #a3a3a3; box-sizing: border-box;" width="100%" height="400" src="https://widgets.2gis.com/widget?type=firmsonmap&amp;options=%7B%22pos%22%3A%7B%22lat%22%3A52.31173212378333%2C%22lon%22%3A104.29900646209718%2C%22zoom%22%3A15%7D%2C%22opt%22%3A%7B%22city%22%3A%22irkutsk%22%7D%2C%22org%22%3A%2270000001006575492%22%7D"></iframe>
						</div>
					</div>
				</div>
			</section>
			<section id="item3" class="cell small-12 cont-section" data-magellan-target="item3">
				<h2>Время работы</h2>
				@include('project.includes.partials.schedule')
			</section>

			<section id="callme" class="cell small-12 cont-section" data-magellan-target="callme">
				<h2>Заказать звонок</h2>
				<p class="extra-head">Мы перезвоним вам в рабочее время</p>
				<div class="grid-x align-center">
					<div class="cell small-12 medium-12 large-8 cls">
						@include('project.includes.forms.call', ['remark' => 'Я бы хотел, чтобы вы мне перезвонили. Есть вопросы, которые мне хочеться обсудить. (Сообщение со страницы КОНТАКТЫ)', 'category_id' => null])
					</div>
				</div>
			</section>

		</div>
	</main>
</div>
@endsection