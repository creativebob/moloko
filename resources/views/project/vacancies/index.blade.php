@extends('project.layouts.app')

@section('title')
<title>{{ $page->title }} | Воротная компания "Марс"</title>
<meta name="description" content="{{ $page->description }} ">
@endsection

@section('content')
<div class="wrap-main grid-x">
	@if (empty($content))
	<h1>наш штат укомплектован и нам никто не требуется.</h1>
	@else
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">Открытые вакансии в нашей компании<a href="#anchor-menu" title="Наверх!"><span class="arrow-top"></span></a></h1>
	</div>
	@endif

	<main class="cell small-12 medium-12 large-12 main-cont">
		@if (!empty($content))
		<ul class="grid-x small-up-1 medium-up-4 large-up-4 teams" data-equalizer data-equalize-by-grid-x="true">
			@foreach ($content as $vacancy)
			<li class="cell">
				<a data-open="mounter-w">
					<img class="thumbnail" src="{{ asset('/project/img/vacancy/mounter.png') }}" alt="{{ $vacancy['position']['position_name']}}">
					<p class="name-person">{{ $vacancy['position']['position_name'] }}</p>
					<span class="myfeedback"></span>
				</a>
			</li>
			@endforeach
		</ul>
		@endif
	</main>
</div>


<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed vacancy" id="mounter-w" data-reveal>
	<h2>Описание вакансии</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/vacancy/mounter.png') }}">
						<p class="name-person">Монтажник уличных ворот</p>
						<!-- 							<p class="status-person">от 40 000 руб.</p> -->
					</div>
				</div>
				<div class="media-object-section">
					<p>Требования: Опыт монтажа секционных гаражных ворот, монтажа и настройки систем автоматизации ворот и шлагбаумов, желание работать и зарабатывать.</p>
					<p>Обязанности: Монтаж секционных гаражных ворот, подключение и настройка автоматики.</p>
					<p>Условия: Оформление согласно ТК РФ, предоставление инструмента, стабильная ЗП вне зависимости от сезона, большой объем работы</p>
					<p>Заработная плата: от 30 000 руб.<p>
						<form action="sendmail.php" method="post" data-abide>
							<fieldset>
								<div class="small-12 medium-12 large-12 cell clean-pad-left">
									<label>Ваш телефон:
										<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
									</label>
								</div>
								<div class="small-12 medium-12 large-12 cell clean-pad-left">
									<label>Ваш комментарий:
										<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
									</label>
								</div>
							</fieldset>
							<fieldset>
								<div class="small-12 cell clean-pad-left">
									<input type="hidden" name="remark" value="Прошу перезвонить, меня интересует вакансия Монтажник секционных ворот. ">
									<input type="submit" class="button small right" value="Отправить">
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


	<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
	<div class="reveal person-feed vacancy" id="mounter2-w" data-reveal>
		<h2>Описание вакансии</h2>
		<div class="raw">
			<div class="cell small-12 medium-12 large-12 large-centered cls">
				<div class="media-object">
					<div class="media-object-section">
						<div class="thumbnail">
							<img src= "{{ asset('/project/img/vacancy/mounter2.png') }}">
							<p class="name-person">Монтажник секционных ворот</p>
							<!-- 							<p class="status-person">от 40 000 руб.</p> -->
						</div>
					</div>
					<div class="media-object-section">
						<p>Требования: Опыт монтажа уличных ворот, монтажа и настройки систем автоматизации ворот и шлагбаумов, желание работать и зарабатывать.</p>
						<p>Обязанности: Производство, сборка, монтаж уличных откатных и распашных ворот, подключение и настройка автоматики.</p>
						<p>Условия: Оформление согласно ТК РФ, предоставление инструмента, стабильная ЗП вне зависимости от сезона, большой объем работы.</p>
						<p>Заработная плата: от 30 000 руб.<p>
							<form action="sendmail.php" method="post" data-abide>
								<fieldset>
									<div class="small-12 medium-12 large-12 cell clean-pad-left">
										<label>Ваш телефон:
											<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
										</label>
									</div>
									<div class="small-12 medium-12 large-12 cell clean-pad-left">
										<label>Ваш комментарий:
											<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
										</label>
									</div>
								</fieldset>
								<fieldset>
									<div class="small-12 cell clean-pad-left">
										<input type="hidden" name="remark" value="Прошу перезвонить, меня интересует вакансия Монтажник уличных откатных ворот. ">
										<input type="submit" class="button small right" value="Отправить">
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

		<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
		<div class="reveal person-feed vacancy" id="manager-w" data-reveal>
			<h2>Описание вакансии</h2>
			<div class="raw">
				<div class="cell small-12 medium-12 large-12 large-centered cls">
					<div class="media-object">
						<div class="media-object-section">
							<div class="thumbnail">
								<img src= "{{ asset('/project/img/vacancy/manager.png') }}">
								<p class="name-person">Менеджер в отдел продаж</p>
								<!-- 							<p class="status-person">от 40 000 руб.</p> -->
							</div>
						</div>
						<div class="media-object-section">
							<p>Требования: коммуникабельность, активность, грамотная речь, презентабельный внешний вид, умение вести переговоры, умение пользоваться персональным компьютером, нацеленность на результат, желание  развиваться и стать профессионалом в отрасли. Главное требование это -  умение заинтересовать клиента, ответственность и исполнительность.</p>
							<p>Обязанности:
								<ul>
									<li>Работа с входящим трафиком клиентов;</li>
									<li>Сопровождение клиентов: консультирование по продукции, расчет стоимости заказа, подготовка и отправка  предложений, заключение договоров, ведение базы клиентов;</li>
									<li>Выполнение плана продаж;</li>
									<li>Взаимодействие с другими отделами компании и с поставщиками, с целью обеспечения выполнения договоренностей с клиентами;</li>
									<li>Ежедневная отчетность, соблюдение скриптов и регламентов компании.</li>
								</ul>
							</p>
							<p>Условия: бесплатное обучение, официальное трудоустройство, достойная заработная плата, 8 часовой рабочий день, дружный коллектив, возможность профессионального и карьерного роста, просторный офис, техническая поддержка в работе и наставничество.</p>
							<p>Пожалуйста, отправьте свое резюме на адрес электронной почты <a href="mailto:info@vorotamars.ru">info@vorotamars.ru</a></p>
							<p>Заработная плата: оклад + % + премии отдела<p>

							</div>
						</div>
					</div>
				</div>
				<button class="close-button" data-close aria-label="Close modal" type="button">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->

			<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
			<div class="reveal person-feed vacancy" id="manager2-w" data-reveal>
				<h2>Описание вакансии</h2>
				<div class="raw">
					<div class="cell small-12 medium-12 large-12 large-centered cls">
						<div class="media-object">
							<div class="media-object-section">
								<div class="thumbnail">
									<img src= "{{ asset('/project/img/vacancy/manager2.png') }}">
									<p class="name-person">Менеджер активных продаж</p>
									<!-- 							<p class="status-person">от 40 000 руб.</p> -->
								</div>
							</div>
							<div class="media-object-section">
								<p>Требования: коммуникабельность, активность, грамотная речь, презентабельный внешний вид, умение вести переговоры на уровне первых лиц компании, умение пользоваться персональным компьютером, нацеленность на результат, желание  развиваться и стать профессионалом в отрасли. Главное требование это -  умение заинтересовать клиента, ответственность и исполнительность.</p>
								<p>Обязанности:
									<ul>
										<li>Активные поиск клиентов - поиск по базе, самостоятельный выход  на компании, строителей, бригадиров, крупных частных клиентов;</li>
										<li>Сопровождение клиентов: консультирование по продукции, расчет стоимости заказа, подготовка и отправка  предложений, заключение договоров, ведение базы клиентов;</li>
										<li>Выезд на объекты к потенциальным заказчикам;</li>
										<li>Выполнение плана продаж;</li>
										<li>Ведение клиентской базы.</li>
									</ul>
								</p>
								<p>Условия: бесплатное обучение, официальное трудоустройство, достойная заработная плата, 8 часовой рабочий день, дружный коллектив, возможность профессионального и карьерного роста, просторный офис, техническая поддержка в работе и наставничество.</p>
								<p>Пожалуйста, отправьте свое резюме на адрес электронной почты <a href="mailto:info@vorotamars.ru">info@vorotamars.ru</a></p>
								<p>Заработная плата: оклад + % + премии отдела</p>
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