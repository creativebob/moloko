<header class="grid-x grid-padding-x">

	<div class="cell small-12 medium-4 large-6 small-order-1 medium-order-1 medium-text-left text-center wrap-header-company-logo">
		<a href="/" title="На главную">
			<img src="{{ $site->company->white->path }}" alt="Логотип Виан-Дизель" title="На главную" class="logo" width="181" height="48">
		</a>
		<p class="prename">{{ $site->company->prename ?? '' }}</p>
	</div>

	<div class="cell small-12 medium-4 large-3 small-order-3 medium-order-2 wrap-header-worktime">
		<div class="grid-x grid-padding-x text-center">
			<div class="cell small-12">
				<button class="button button-modal-call" data-open="modal-call">Записаться на диагностику</button>
				@include('mwtour.layouts.headers.includes.modal_call')
			</div>
			<div class="cell small-12 wrap-schedule" id="schedule">
				@include('project.composers.worktimes.today')
			</div>
		</div>
	</div>

	<div class="cell small-12 medium-4 large-3 small-order-2 medium-order-3 wrap-header-filial">
		<div class="grid-x grid-padding-x medium-text-right text-center">
			<div class="cell small-12 wrap-phone">

				@isset($site->filial->main_phone)
					<a href="tel:{{ callPhone($site->filial->main_phone->phone) }}" class="phone">{{ decorPhone($site->filial->main_phone->phone) }}</a>
				@endisset

			</div>
			<div class="cell small-12 wrap-address">
				<a href="/contacts">
					<span>
						<span class="icon icon-location"></span>
						@isset($site->filial->location)

							г. {{ $site->filial->location->city->name }},
							{{ $site->filial->location->address ?? '' }}
						@endisset
					</span>
				</a>
			</div>
			<div class="cell small-12 wrap-cart">
				<cart-header-component></cart-header-component>
			</div>
		</div>
	</div>
</header>