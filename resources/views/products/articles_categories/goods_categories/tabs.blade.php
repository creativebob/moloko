@can('index', App\Raw::class)
<li class="tabs-title">
	<a data-tabs-target="tab-raws" href="#tab-raws">Состав</a>
</li>
@endcan

@can('index', App\Goods::class)
<li class="tabs-title">
    <a data-tabs-target="tab-related" href="#tab-related">Сопутствующие</a>
</li>
@endcan
