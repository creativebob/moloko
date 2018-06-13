{{-- @php
$checked = '';
@endphp

@if (in_array($product->id, $product_metrics))
@php
$checked = 'checked';
@endphp
@endif --}}

<li class="checkbox">
	{{ Form::checkbox('add_product_id', $product->id, null, ['class' => 'add-product', 'id' => 'add-product-'. $product->id]) }}
	<label for="add-product-{{ $product->id }}"><span>{{ $product->name }}</span></label>
</li>

