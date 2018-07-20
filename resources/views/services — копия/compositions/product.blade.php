@php
$checked = '';
@endphp

@if (in_array($product['id'], $products_category_compositions))
@php
$checked = 'checked';
@endphp
@endif

<li class="checkbox">
	{{ Form::checkbox('add_products_id', $product['id'], null, ['class' => 'add-composition', 'id' => 'add-product-'.$product['id'], $checked]) }}
	<label for="add-product-{{ $product['id'] }}"><span>{{ $product['name'] }}</span></label>
</li>

