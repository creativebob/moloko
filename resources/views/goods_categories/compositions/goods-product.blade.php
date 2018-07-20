@php
$checked = '';
@endphp

@if (in_array($goods_product['id'], $goods_category_compositions))
@php
$checked = 'checked';
@endphp
@endif

<li class="checkbox">
	{{ Form::checkbox('add_products_id', $goods_product['id'], null, ['class' => 'add-composition', 'id' => 'add-goods-product-'.$goods_product['id'], $checked]) }}
	<label for="add-goods-product-{{ $goods_product['id'] }}"><span>{{ $goods_product['name'] }}</span></label>
</li>

