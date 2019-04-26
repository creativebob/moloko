@php
$checked = '';
@endphp

{{-- @if (in_array($raws_product['id'], $raws_category_compositions))
@php
$checked = 'checked';
@endphp
@endif --}}

<li class="checkbox">
	{{ Form::checkbox('add_products_id', $raw['id'], null, ['class' => 'add-composition', 'id' => 'add-raws-product-'.$raw['id']]) }}
	<label for="add-raws-product-{{ $raw['id'] }}"><span>{{ $raw['name'] }}</span></label>
</li>

