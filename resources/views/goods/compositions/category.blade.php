@php

$name = isset($composition_articles->first()->raws_product_id) ? 'compositions' : 'set_compositions';

@endphp

<li>
	<span class="parent" data-open="composition_category-{{ $composition_articles->first()->product->category->id }}">{{ $composition_articles->first()->product->category->name }}</span>
	<div class="checker-nested" id="composition_category-{{ $composition_articles->first()->product->category->id }}">
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

