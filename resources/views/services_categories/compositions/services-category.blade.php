
@if (($services_cat['services_products_count'] > 0) || isset($services_cat['children']))
<li>
	<span class="parent" data-open="services_cat-{{ $services_cat['id'] }}">{{ $services_cat['name'] }}</span>
	<div class="checker-nested" id="services_cat-{{ $services_cat['id'] }}">
		<ul class="checker">
			@if ($services_cat['services_products_count'] > 0)

			@foreach ($services_cat['services_products'] as $services_product)
			@include('services_categories.compositions.services-product', $services_product)
			@endforeach

			@endif
			@if (isset($services_cat['children']))

			@foreach ($services_cat['children'] as $services_cat)
			@include('services_categories.compositions.services-category', $services_cat)
			@endforeach

			@endif
			
		</ul>
	</div>
</li>
@endif
