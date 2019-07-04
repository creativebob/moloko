<li class="tabs-title">
	<a data-tabs-target="catalogs" href="#catalogs">Каталоги</a>
</li>

@if($process->set)
	<li class="tabs-title">
		<a data-tabs-target="services" href="#services">Услуги</a>
	</li>
	@else
<li class="tabs-title">
	<a data-tabs-target="workflows" href="#workflows">Рабочие процессы</a>
</li>
	@endif