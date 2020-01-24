<li class="tabs-title">
	<a data-tabs-target="tab-prices" href="#tab-prices">Расположение в прайсах</a>
</li>

@if($article->kit)
	<li class="tabs-title">
		<a data-tabs-target="tab-goods" href="#tab-goods">Набор товаров</a>
	</li>
@else
	<li class="tabs-title">
		<a data-tabs-target="tab-raws" href="#tab-raws">Состав</a>
	</li>
@endif

<li class="tabs-title">
	<a data-tabs-target="tab-containers" href="#tab-containers">Упаковка</a>
</li>

<li class="tabs-title">
	<a data-tabs-target="tab-attachments" href="#tab-attachments">Вложения</a>
</li>
