@if ($categories->isNotEmpty())
@foreach($categories as $category)
@if ($category->articles->isNotEmpty())
<li>
	<span class="parent" data-open="composition_category">{{ $category->name }}</span>
	<div class="checker-nested" id="composition_category">
		<ul class="checker">

			@foreach($category->articles as $article)
			<li class="checkbox">
				{{ Form::checkbox(null, $article->id, null, ['class' => 'add-composition', 'id' => 'add-composition-'.$article->id]) }}
				<label for="add-composition-{{ $article->id }}">
					<span>{{ $article->name }}</span>
				</label>
			</li>
			@endforeach

		</ul>
	</div>
</li>
@endif
@endforeach
@else
<li>Ничего нет...</li>
@endif

