@php
$checked = '';
@endphp

@if (in_array($composition->id, $product_compositions))
@php
$checked = 'checked';
@endphp
@endif

<li class="checkbox">
	{{ Form::checkbox('add_product_id', $composition->id, null, ['class' => 'add-composition', 'id' => 'add-composition-'. $composition->id, $checked]) }}
	<label for="add-composition-{{ $composition->id }}"><span>{{ $composition->name }}</span></label>
</li>

