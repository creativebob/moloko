		{{-- @isset($indicators_company_list) --}}
			<ul class="grid-x grid-padding-x indicators-company-list small-up-2 medium-up-4 large-up-4">
				{{-- @foreach($indicators_company_list as $item) --}}


						<li class="cell indicators-company-item" data-equalizer-watch>
							<div class="grid-x lsd">
								<div class="cell medium-12 large-6 digit">{{ num_format(5, 0) }}</div>
								<div class="cell medium-12 large-6 digit-description">лет на рынке региона</div>
							</div>
						</li>

						<li class="cell indicators-company-item" data-equalizer-watch>
							<div class="grid-x lsd">
								<div class="cell medium-12 large-6 digit">{{ num_format(37, 0) }}</div>
								<div class="cell medium-12 large-6 digit-description">проверенных производителя</div>
							</div>
						</li>

						<li class="cell indicators-company-item" data-equalizer-watch>
							<div class="grid-x lsd">
								<div class="cell medium-12 large-6 digit">{{ num_format(1500, 0) }}</div>
								<div class="cell medium-12 large-6 digit-description">довольных клиентов</div>
							</div>
						</li>
		
						<li class="cell indicators-company-item" data-equalizer-watch>
							<div class="grid-x lsd">
								<div class="cell medium-12 large-6 digit">{{ num_format(20000, 0) }}</div>
								<div class="cell medium-12 large-6 digit-description">новогодних подарков в год</div>
							</div>
						</li>

				{{-- @endforeach --}}
			</ul>
		{{-- @endisset --}}