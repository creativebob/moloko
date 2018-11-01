@php
if ($composition_articles->first()->raws_product_id) {

$category_id = $composition_articles->first()->raws_product->raws_category->id;
$category_name = $composition_articles->first()->raws_product->raws_category->name;
$name = 'compositions';

} else {

$category_id = $composition_articles->first()->goods_product->goods_category->id;
$category_name = $composition_articles->first()->goods_product->goods_category->name;
$name = 'set_compositions';

}
@endphp

<li>
	<span class="parent" data-open="composition_category-{{ $category_id }}">{{ $category_name }}</span>
	<div class="checker-nested" id="composition_category-{{ $category_id }}">
		<ul class="checker">

			@foreach($composition_articles as $article)

			<li class="checkbox">
				{{ Form::checkbox($name.'[]', $article->id, null, ['class' => 'add-composition', 'id' => 'add-composition-'.$article->id]) }}
				<label for="add-composition-{{ $article->id }}"><span>{{ $article->name }}</span></label>
			</li>

			@endforeach

		</ul>
	</div>
</li>

