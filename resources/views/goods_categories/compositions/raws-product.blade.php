

<li class="checkbox">
	{{ Form::checkbox('add_products_id', $raws_product['id'], null, ['class' => 'add-composition', 'id' => 'add-raws-product-'.$raws_product['id'], $checked]) }}
	<label for="add-raws-product-{{ $raws_product['id'] }}"><span>{{ $raws_product['name'] }}</span></label>
</li>

