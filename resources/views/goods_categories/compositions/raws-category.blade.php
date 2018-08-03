
<li>
	<span class="parent" data-open="composition_category-{{ $composition_category['id'] }}">{{ $composition_category['name'] }}</span>
	<div class="checker-nested" id="composition_category-{{ $composition_category['id'] }}">
		<ul class="checker">

			@if ($composition_category['raws_products_count'] > 0)
			@foreach ($composition_category['raws_products'] as $raws_product)

			@if(count($raws_product['raws']) > 0)

			@foreach ($raws_product['raws'] as $raw)

			@php
			$checked = '';
			@endphp

			@if (in_array($raw['id'], $goods_category_compositions))
			@php
			$checked = 'checked';
			@endphp
			@endif

			<li class="checkbox">
				{{ Form::checkbox('add_products_id', $raw['id'], null, ['class' => 'add-composition', 'id' => 'add-raws-'.$raw['id'], $checked]) }}
				<label for="add-raws-{{ $raw['id'] }}"><span>{{ $raw['name'] }}</span></label>
			</li>
			@endforeach

			@endif

			@endforeach
			@endif

			@if (isset($composition_category['children']))

			@foreach ($composition_category['children'] as $composition_category)
			@include('goods_categories.compositions.raws-category', $composition_category)
			@endforeach

			@endif

			
			
		</ul>
	</div>
</li>

