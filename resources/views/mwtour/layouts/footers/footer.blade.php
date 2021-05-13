<footer class="grid-x grid-padding-x" id="footer">

	<div class="cell small-12 medium-4 medium-text-left text-center wrap-footer-filial">
		<span>&copy; @isset($site->company->foundation_date){{ $site->company->foundation_date->format('Y') . ' -' }} @endisset {{ date("Y") }} "{{ $site->company->name }}"</span>
		<br>

		@isset($site->company->name_legal)
			<span class="color-gray">{{ $site->company->legal_form->name ?? '' }} {{ $site->company->name_legal }}</span>
			<br>
		@endisset

		@isset($site->company->inn)
			<span class="color-gray">ИНН / ОГРН: {{ $site->company->inn ?? '' }} / {{ $site->company->ogrn  ?? '' }}</span>
			<br><br>
		@endisset

		@isset($site->company->location->city)
			<span>{{ $site->company->location->city->name  ?? '' }}, {{ $site->company->location->address ?? '' }}</span>
			<br>
		@endisset

		@isset($site->filial->email)
			<span><span class="icon icon-email"></span><a href="mailto:{{ $site->filial->email }}" title="Напишите нам!">{{ $site->filial->email }}</a></span>
			<br>
		@endisset

	</div>

	<div class="cell small-12 medium-4 text-center wrap-footer-logo">
		<img src="/img/mwtour/slogan.svg" class="slogan" alt="Живи, твори, мечтай!" width="230" height="48"><br>
		{{-- @include('project.composers.navigations.navigation_by_align', ['align' => 'bottom']) --}}
	</div>

	<div class="cell small-12 medium-4 footer-studio">
		<span class="studio-info"><span class="color-gray">Разработка сайта: </span><a>Creative<span class="color-red">Bob</span> Studio</a></span>
	</div>

</footer>