<li class="tabs-title">
	<a data-tabs-target="catalogs" href="#catalogs">Каталоги</a>
</li>

@if($article->set)
	<li class="tabs-title">
		<a data-tabs-target="goods" href="#goods">Товары</a>
	</li>
	@else
<li class="tabs-title">
	<a data-tabs-target="raws" href="#raws">Сырье</a>
</li>
	@endif