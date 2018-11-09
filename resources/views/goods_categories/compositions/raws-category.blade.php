<li>
	<span class="parent" data-open="composition_category-{{ $composition_articles->first()->raws_product->raws_category->id }}">{{ $composition_articles->first()->raws_product->raws_category->name }}</span>
	<div class="checker-nested" id="composition_category-{{ $composition_articles->first()->raws_product->raws_category->id }}">
		<ul class="checker">

			@foreach($composition_articles as $raws_article)

			<li class="checkbox">
				{{ Form::checkbox('compositions[]', $raws_article->id, null, ['class' => 'add-composition', 'id' => 'add-composition-'.$raws_article->id]) }}
				<label for="add-composition-{{ $raws_article['id'] }}"><span>{{ $raws_article->name }}</span></label>
			</li>

			@endforeach

		</ul>
	</div>
</li>

