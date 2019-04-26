@if ($categories->isNotEmpty())
@foreach($categories as $category)
@if ($category->articles->isNotEmpty())
<li>
	<span class="parent" data-open="composition_category-{{ $category->id }}">{{ $category->name }}</span>
	<div class="checker-nested" id="composition_category-{{ $category->id }}">
		<ul class="checker">

			@foreach($category->articles as $composition)
			<li class="checkbox">
				{{ Form::checkbox(null, $composition->id, in_array($composition->id, $article->compositions->pluck('id')->toArray()), ['class' => 'add-composition', 'id' => 'add-composition-'.$composition->id]) }}
				<label for="add-composition-{{ $composition->id }}">
					<span>{{ $composition->name }}</span>
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

