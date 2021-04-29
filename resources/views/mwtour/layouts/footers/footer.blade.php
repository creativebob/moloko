<footer class="grid-x grid-padding-x" id="footer">

	<div class="cell small-12 medium-4 medium-text-left text-center wrap-footer-filial">
		<span>&copy; @isset($site->company->foundation_date){{ $site->company->foundation_date->format('Y') . ' -' }} @endisset {{ date("Y") }} "{{ $site->company->legal_form->name }} {{ $site->company->name }}"</span>
		<br>

		@isset($site->company->prename)
			<span>{{ $site->company->prename }}</span>
			<br>
		@endisset

		@isset($site->filial->email)
			<span><span class="icon icon-email"></span><a href="mailto:{{ $site->filial->email }}" title="Напишите нам!">{{ $site->filial->email }}</a></span>
			<br>
		@endisset

	</div>

	<div class="cell small-12 medium-4 text-center wrap-footer-logo">
		<img src="{{ $site->company->white->path }}"  class="logo-small" alt="Логотип ВИАН Дизель" width="121" height="32"><br>
		@include('project.composers.navigations.navigation_by_align', ['align' => 'bottom'])
	</div>

	<div class="cell small-12 medium-4 block-footer-3">
		<span>Разработка сайта: <a>Creative<span class="color-red">Bob</span> Studio</a></span>
	</div>

</footer>