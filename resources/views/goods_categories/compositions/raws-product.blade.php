@php
$checked = '';
@endphp

{{-- @if (in_array($raws_product['id'], $raws_category_compositions))
@php
$checked = 'checked';
@endphp
@endif --}}

<li class="checkbox">
	{{ Form::checkbox('add_products_id', $raws_product['id'], null, ['class' => 'add-composition', 'id' => 'add-raws-product-'.$raws_product['id']]) }}
	<label for="add-raws-product-{{ $raws_product['id'] }}"><span>{{ $raws_product['name'] }}</span></label>
</li>

