{{-- Свойства --}}
<div class="tabs-panel" id="properties">
	@include('goods_categories.metrics.section')
</div>

{{-- Свойства для набора --}}
{{-- <div class="tabs-panel" id="set-properties">

@include('includes.metrics_category.section', ['category' => $goods_category, 'set_status' => 'set'])

</div> --}}

{{-- Исключаем состав из сырья --}}

{{-- Состав --}}



<div class="tabs-panel" id="compositions">

	@include('goods_categories.compositions.compositions', ['category' => $category])
</div>

