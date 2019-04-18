@extends('project.layouts.app')

@section('title')
<title>Инструкции | Воротная компания "Марс"</title>
<meta name="description" content="Контактные данные воротной компании Марс. Телефон в Иркутске: 8 (3952) 71-77-75">
@endsection

@section('content')
<div class="grid-x grid-padding-x">
	<div class="cell small-12 medium-12 large-12">
		<nav aria-label="You are here:" role="navigation">
			<ul class="breadcrumbs">
				<li><a href="/knowledge">База знаний</a></li>
				<li class="disabled">Инструкции</li>
			</ul>
		</nav>
	</div>
</div>



<div class="wrap-main grid-x">
	<div class="cell small-12 medium-12 large-12" data-sticky-container  id="foo2">
		<h1 data-sticky data-top-anchor="foo2:top" data-options="marginTop:1.6;">Инструкции по монтажу и эксплуатации<span class="chapters"></span><a href="#anchor-menu" title="Наверх!"><span class="arrow-top"></span></a></h1>
	</div>

	<main class="cell small-12 medium-12 large-12 main-cont">
		<div class="grid-x">
			<div class="cell small-12 medium-6 large-4">
				<img src="{{ asset('/project/img/mangirl.png') }}">
			</div>
			<div class="cell small-12 medium-6 large-8">
				<ul class="vertical menu mymanual" data-drilldown>
					<li>
						<a href="#Item-1">Автоматика и аксессуары</a>
						<ul class="vertical menu">
							<li><a href="#Item-1A">Аксессуары</a>
								<ul class="vertical menu">
									<li>
										<a href="#Item-1A">SIM-контроллер GSM</a>
										<ul class="vertical menu">
											<li>
												<a href="#Item-1A">Конфигуратор</a>
												<ul class="vertical menu">
													<li><a href="/project/guides/automatic/accessories/GSM/configurator/setup.exe" download class="download2">Setup (748 kb, exe)</a></li>
												</ul>
											</li>
											<li><a href="/project/guides/automatic/accessories/GSM/GSM(VRT012).pdf" download class="download2">GSM VRT012 (1798 kb, pdf)</a></li>
										</ul>

									</li>

									<li>
										<a href="#Item-1A">Ключ-кнопка</a>
										<ul class="vertical menu">
											<li><a href="/project/guides/automatic/accessories/button-key/instruct_KEYSWITCH.pdf" download class="download2">Инструкция KEYSWITCH (278 kb, pdf)</a></li>
											<li><a href="/project/guides/automatic/accessories/button-key/instruct_SWM.pdf" download class="download2">Инструкция SWM(126 kb, pdf)</a></li>
										</ul>
									</li>

									<li>
										<a href="#Item-1A">Фотоэлементы</a>
										<ul class="vertical menu">
											<li><a href="/project/guides/automatic/accessories/photoelements/instruct_photoelem.pdf" download class="download2">Инструкция Photo-R (182 kb, pdf)</a></li>
											<li><a href="/project/guides/automatic/accessories/photoelements/instruct_photocell.pdf" download class="download2">Инструкция PhotoCell (91 kb, pdf)</a></li>
										</ul>
									</li>
								</ul>
							</li>
							<li>
								<a href="#Item-1A">Инструкции по эксплуатации</a>
								<ul class="vertical menu">
									<li>
										<a href="#Item-1A">Инструкциии по эксплуатации приводов для сдвижных ворот</a>
										<ul class="vertical menu">
											<li><a href="/project/guides/automatic/instruct_operation/Instruct_sliding/Instruct_SLIDIN-1300.pdf" download class="download2">Инструкция SLIDIN-1300 (1 732 kb, pdf)</a></li>
											<li><a href="/project/guides/automatic/instruct_operation/Instruct_sliding/Instruct_SLIDIN-2100.pdf" download class="download2">Инструкция SLIDIN-2100 (1 728 kb, pdf)</a></li>
										</ul>
									</li>


									<li>
										<a href="#Item-1A">Инструкциии по эксплуатации приводов для секционных ворот</a>
										<ul class="vertical menu">
											<li><a href="/project/guides/automatic/instruct_operation/Instruct_sectional/Instruct_SE-750.pdf" download class="download2">Инструкция SE-750 (957 kb, pdf)</a></li>
											<li><a href="/project/guides/automatic/instruct_operation/Instruct_sectional/Instruct_SHAFT-30.pdf" download class="download2">Инструкция SHAFT-30 (427 kb, pdf)</a></li>
										</ul>
									</li>
									<li>
										<a href="#Item-1A">Инструкциии по эксплуатации шлагбаума</a>
										<ul class="vertical menu">
											<li><a href="/project/guides/automatic/instruct_operation/Instruct_barrier/Instruct_Barrier_5000.pdf" download class="download2">Инструкция Barrier 5000 (472 kb, pdf)</a></li>
										</ul>
									</li>
								</ul>
							</li>
							<li>
								<a href="#Item-1A">Приводы для распашных ворот</a>
								<ul class="vertical menu">
									<li><a href="/project/guides/automatic/gatedrive_swing/Manual_DoorHan_Arm-320_RUS_A4.pdf" download class="download2">Инструкция Arm-320 (848 kb, pdf)</a></li>
									<li><a href="/project/guides/automatic/gatedrive_swing/Manual_DoorHan_Swing_3000_5000_RUS_A4.pdf" download class="download2">Инструкция Swing_3000_5000 (1 219 kb, pdf)</a></li>
								</ul>
							</li>
							<li>
								<a href="#Item-1A">Приводы для сдвижных ворот</a>
								<ul class="vertical menu">
									<li><a href="/project/guides/automatic/gatedrive_street/Instruct_Sliding_800.pdf" download class="download2">Инструкция Sectional 500 (1 047 kb, pdf)</a></li>
									<li><a href="/project/guides/automatic/gatedrive_street/Instruct_Sliding_1300-2100.pdf" download class="download2">Инструкция Sectional 750-1200-750FAST (5 831 kb, pdf)</a></li>
									<li><a href="/project/guides/automatic/gatedrive_street/Instruct_Rack.pdf" download class="download2">Инструкция зубчатой рейки (60 kb, pdf)</a></li>
								</ul>
							</li>
							<li>
								<a href="#Item-1A">Приводы для секционных ворот</a>
								<ul class="vertical menu">
									<li>
										<a href="#Item-1A">Серия Sectional (Потолочные)</a>
										<ul class="vertical menu">
											<li><a href="/project/guides/automatic/gatedrive_sectional/Series_Sectional/Instruct_Sectional_500.pdf" download class="download2">Инструкция Sectional 500 (781 kb, pdf)</a></li>
											<li><a href="/project/guides/automatic/gatedrive_sectional/Series_Sectional/Instruct_Sectional_750-1200-750FAST.pdf" download class="download2">Инструкция Sectional 750-1200-750FAST (1 041 kb, pdf)</a></li>
										</ul>
									</li>
									<li>
										<a href="#Item-1A">Серия Shaft (Вальные)</a>
										<ul class="vertical menu">
											<li><a href="/project/guides/automatic/gatedrive_sectional/Series_Shaft/Instruct_Shaft-30.pdf" download class="download2">Инструкция Shaft-30 (604 kb, pdf)</a></li>
											<li><a href="/project/guides/automatic/gatedrive_sectional/Series_Shaft/Instruct_Shaft-60.pdf" download class="download2">Инструкция Shaft-60 (1 405 kb, pdf)</a></li>
											<li><a href="/project/guides/automatic/gatedrive_sectional/Series_Shaft/Instruct_Shaft-120.pdf" download class="download2">Инструкция Shaft-120 (1 853 kb, pdf)</a></li>
											<li><a href="/project/guides/automatic/gatedrive_sectional/Series_Shaft/Instruct_Extra_device.pdf" download class="download2">Инструкция на дополнительные устройства (322 kb, pdf)</a></li>
										</ul>
									</li>
								</ul>
							</li>

							<li>
								<a href="#Item-1A">Шлагбаумы</a>
								<ul class="vertical menu">
									<li><a href="/project/guides/automatic/barriers/Instruct_Barrier_5000.pdf" download class="download2">Инструкция Barrier 5000 (4 704 kb, pdf)</a></li>
									<li><a href="/project/guides/automatic/barriers/Instruct_Barrier_N.pdf" download class="download2">Инструкция N (834 kb, pdf)</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li>
						<a href="#Item-1">Гаражные ворота</a>
						<ul class="vertical menu">
							<li><a href="/project/guides/garagegates/Instruct_ISD01.pdf" download class="download2">Инструкция по монтажу промышленные секционные ворота ISD01  (5 379 kb, pdf)</a></li>
							<li><a href="/project/guides/garagegates/Instruct_RSD01.pdf" download class="download2">Инструкция по монтажу секционные ворота RSD01  (16 466 kb, pdf)</a></li>
							<li><a href="/project/guides/garagegates/Instruct_RSD02.pdf" download class="download2">Инструкция по монтажу секционные ворота RSD02  (19 678 kb, pdf)</a></li>
						</ul>
					</li>
					<li>
						<a href="#Item-1">Уличные ворота</a>
						<ul class="vertical menu">
							<li><a href="/project/guides/streetgates/Instruct_Streetgates_Doorhan.pdf" download class="download2">Инструкция по сборке и монтажу уличных откатных ворот Doorhan  (2 021 kb, pdf)</a></li>
							<li><a href="/project/guides/streetgates/Operation_Streetgates_Doorhan.pdf" download class="download2">Инструкция по эксплуатации уличных откатных ворот Doorhan  (219 kb, pdf)</a></li>
							<li><a href="/project/guides/streetgates/Do_it_yourself_Streetgates_Doorhan.pdf" download class="download2">Инструкция "Собери сам - Ноябрь 2014" (6 996 kb, pdf)</a></li>
							<!-- 						      <li><a href="#Item-1A">Монтаж откатных ворот DoorHan</a></li> -->
							<li><a href="/project/guides/streetgates/Circuit_training_Foundation_2.jpg" download class="download2">Схема подготовки основания для откатных ворот с двумя столбами (280 kb, jpg)</a></li>
							<li><a href="/project/guides/streetgates/Circuit_training_Foundation_3.jpg" download class="download2">Схема подготовки основания для откатных ворот с тремя столбами (283 kb, jpg)</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</main>
</div>
@endsection