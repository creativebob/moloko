<li class="tabs-title">
	<a data-tabs-target="catalogs" href="#catalogs">Расположение в прайсах</a>
</li>

@if($article->kit)
	<li class="tabs-title">
		<a data-tabs-target="goods" href="#goods">Набор товаров</a>
	</li>
@else
	<li class="tabs-title">
		<a data-tabs-target="raws" href="#raws">Состав</a>
	</li>
@endif