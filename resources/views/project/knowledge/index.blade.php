@extends('project.layouts.app')

@section('title')
<title>{{ $page->title }} | Воротная компания "Марс"</title>
<meta name="description" content="{{ $page->description }} ">
@endsection

@section('content')
<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">База Знаний<a href="#anchor-menu" title="Наверх!"><span class="arrow-top"></span></a></h1>
	</div>

<!-- 			<aside class="cell small-12 medium-3 large-3 left-sidebar" id="topAnchor">
				<div data-sticky-container>
					<div class="sticky" id="example" data-sticky data-options="marginTop:8;" data-top-anchor="topAnchor" data-btm-anchor="item10:bottom">
						<nav data-magellan data-bar-offset="110">
							<ul class="menu vertical specific">
								<li><a href="#item1">Важен ли материал гаражного проема?</a></li>
							</ul>
						</nav>
					</div>
				</div>
			</aside> -->

			<main class="cell small-12 main-cont">
				<div class="featured-image-block-grid">
					<div class="featured-image-block-grid-header small-12 medium-12 large-12 cell text-center">
						<h2>Наш видеоблог о воротах</h2>
						<p>Все что вы хотели узнать, но стеснялись спросить</p>
					</div>
					<div class="grid-x grid-padding-x large-up-4 small-up-2">
						<div class="featured-image-block cell">
							<a href="/knowledge/base_garage_gates">
								<img src="{{ asset('/project/img/know/222.jpg') }}" />
								<p class="text-center featured-image-block-title">Гаражные ворота</p>
							</a>
							<p class="text-center desk-know">Рассказываем о конструктиве, материале и о том, что нужно знать при выборе ворот</p>
						</div>

						<div class="featured-image-block cell">
							<a href="/knowledge/base_street_gates">
								<img src="{{ asset('/project/img/know/3333.jpg') }}" />
								<p class="text-center featured-image-block-title">Уличные ворота</p>
							</a>
							<p class="text-center desk-know">Что представляют собой откатные и распашные ворота. В чем сложность конструкции.</p>
						</div>

						<div class="featured-image-block cell">
							<a href="/knowledge/base_answers">
								<img src="{{ asset('/project/img/know/444.jpg') }}" />
								<p class="text-center featured-image-block-title">Частые вопросы</p>
							</a>
							<p class="text-center desk-know">Видеоответы на часто задаваемые вопросы в одном разделе. Спрашивайте и мы ответим.</p>
						</div>

						<div class="featured-image-block cell">
							<a href="/knowledge/guide">
								<img src="{{ asset('/project/img/know/5555.jpg') }}" />
								<p class="text-center featured-image-block-title">Инструкции</p>
							</a>
							<p class="text-center desk-know">Поможем собраться с мыслями, и, наконец, собрать свои ворота своими ловкими и умелыми руками</p>
						</div>

						<div class="featured-image-block cell spirit">
							<a href="#">
								<img src="{{ asset('/project/img/know/666.jpg') }}" />
								<p class="text-center featured-image-block-title">Подготовка проема</p>
							</a>
							<p class="text-center desk-know">Подгогтовка проема гаражных ворот, производство основания для уличных - основа качественного монтажа</p>
						</div>

						<div class="featured-image-block cell spirit">
							<a href="#">
								<img src="{{ asset('/project/img/know/888.jpg') }}" />
								<p class="text-center featured-image-block-title">Видео отзывы</p>
							</a>
							<p class="text-center desk-know">Наши клиенты говорят о своих впечатлениях о новых воротах, о впечатлениях при работе с нами</p>
						</div>

						<div class="featured-image-block cell">
							<a href="/knowledge/another_city">
								<img src="{{ asset('/project/img/know/999.jpg') }}" />
								<p class="text-center featured-image-block-title">Ворота в другой город</p>
							</a>
							<p class="text-center desk-know">Рассказываем о том, как вам можно купить ворота если вы находитесь далеко от центральных городов.</p>
						</div>

						<div class="featured-image-block cell spirit">
							<a href="#">
								<img src="{{ asset('/project/img/know/777.jpg') }}" />
								<p class="text-center featured-image-block-title">Живем и работаем в Иркутске</p>
							</a>
							<p class="text-center desk-know">Бодрое видео о нашей компании работающей в Сибирском городке</p>
						</div>
					</div>
				</div>
			</main>
		</div>

		<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
		<div class="reveal person-feed" id="EU" data-reveal>
			<h2>Обратная связь</h2>
			<div class="raw">
				<div class="cell small-12 medium-12 large-12 large-centered cls">
					<div class="media-object">
						<div class="media-object-section">
							<div class="thumbnail">
								<img src= "{{ asset('/project/img/personal/person-10.png">
								<p class="name-person">Евгений Утебасов</p>
								<p class="status-person">Специалист по замеру</p>
							</div>
						</div>
						<div class="media-object-section">
							<blockquote>"Самое время произвести замер! Не так ли?"</blockquote>
							<p>Готов поведать о всех тонкостях воротного производства и монтажа. Ваша экономия - моя задача. Расскажу о материалах, помогу выбрать конструктив. Ну, и конечно, сделаю точный замер.</p>
							<form action="sendmaster.php" method="post" data-abide>
								<fieldset>
									<div class="small-12 medium-6 large-6 cell clean-pad-left">
										<label>Дата замера:
											<input type="text" id="date" class="datezamer" name="date_zamer" value="18.07.2016">
										</label>
									</div>

									<div class="small-12 medium-6 large-6 cell clean-pad-left">
										<label>Удобное время:
											<input type="text" id="tz-begin" maxlength="5" class="time-field" pattern="([0-1][0-9]|[2][0-3]):[0-5][0-9]" placeholder="10:00"  onkeyup="proTime(this);" name="time_zamer">
										</label>
									</div>

									<div class="small-12 medium-12 large-12 cell clean-pad-left">
										<label>Адрес где будет производиться замер:
											<input name="user_address" type="text" placeholder="" value="" required maxlength="50">
										</label>
									</div>

									<div class="small-12 medium-12 large-12 cell clean-pad-left">
										<label>Ваше имя:
											<input name="user_name" type="text" placeholder="" value="" required maxlength="24">
										</label>
									</div>
									<div class="small-12 medium-12 large-12 cell clean-pad-left">
										<label>Телефон:
											<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
										</label>
									</div>
								</fieldset>
								<fieldset>
									<div class="small-12 cell clean-pad-left">
										<input type="submit" class="button small right" value="Отправить заявку на замер!">
									</div>
								</fieldset>
							</form>
						</div>
					</div>
				</div>
			</div>
			<button class="close-button" data-close aria-label="Close modal" type="button">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->
		@endsection