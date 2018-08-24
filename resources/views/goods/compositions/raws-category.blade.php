
<li>
	<span class="parent" data-open="composition_category-{{ $composition_category['id'] }}">{{ $composition_category['name'] }}</span>
	<div class="checker-nested" id="composition_category-{{ $composition_category['id'] }}">
		<ul class="checker">

			@if ($composition_category['raws_products_count'] > 0)
			@foreach ($composition_category['raws_products'] as $raws_product)

			@if(count($raws_product['raws_articles']) > 0)

			@foreach ($raws_product['raws_articles'] as $raws_article)

			@php
			$checked = '';
			@endphp

			@if (in_array($raws_article['id'], $cur_goods_compositions))
			@php
			$checked = 'checked';
			@endphp
			@endif

			<li class="checkbox">
				{{ Form::checkbox('add_products_id', $raws_article['id'], null, ['class' => 'add-composition', 'id' => 'add-raws_article-'.$raws_article['id'], $checked]) }}
				<label for="add-raws_article-{{ $raws_article['id'] }}"><span>{{ $raws_article['name'] }}</span></label>
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

