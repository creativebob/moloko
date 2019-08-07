

@if ($containers_categories->isNotEmpty())

@foreach($containers_categories as $containers_category)

@if ($containers_category->containers->isNotEmpty())
<li>
	<span class="parent" data-open="container_category-{{ $containers_category->id }}">{{ $containers_category->name }}</span>
	<div class="checker-nested" id="container_category-{{ $containers_category->id }}">
		<ul class="checker">

			@foreach($containers_category->containers as $container)
				@if(isset($container->article))
					<li class="checkbox">
						{{ Form::checkbox(null, $container->id, in_array($container->id, $article->containers->pluck('id')->toArray()), ['class' => 'add-container', 'id' => 'container-' . $container->id]) }}

							<label for="container-{{ $container->id }}">
								<span>{{ $container->article->name }}</span>
							</label>

					</li>
				@endif
			@endforeach

		</ul>
	</div>
</li>
@endif

@endforeach

@else
<li>Ничего нет...</li>
@endif
