@php
$checked = '';
@endphp

@if (in_array($services_product['id'], $services_category_compositions))
@php
$checked = 'checked';
@endphp
@endif

<li class="checkbox">
	{{ Form::checkbox('add_products_id', $services_product['id'], null, ['class' => 'add-composition', 'id' => 'add-services-product-'.$services_product['id'], $checked]) }}
	<label for="add-services-product-{{ $services_product['id'] }}"><span>{{ $services_product['name'] }}</span></label>
</li>

