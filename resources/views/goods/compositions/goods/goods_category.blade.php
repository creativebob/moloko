<li>
	<span class="parent" data-open="composition_category-{{ $composition_articles->first()->goods_product->goods_category->id }}">{{ $composition_articles->first()->goods_product->goods_category->name }}</span>
	<div class="checker-nested" id="composition_category-{{ $composition_articles->first()->goods_product->goods_category->id }}">
		<ul class="checker">

			@foreach($composition_articles as $goods_article)

			<li class="checkbox">
				{{ Form::checkbox('compositions[]', $goods_article->id, null, ['class' => 'add-composition', 'id' => 'add-composition-'.$goods_article->id]) }}
				<label for="add-composition-{{ $goods_article['id'] }}"><span>{{ $goods_article->name }}</span></label>
			</li>

			@endforeach

		</ul>
	</div>
</li>

