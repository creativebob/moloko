@if ($raws_categories->isNotEmpty())

	@foreach($raws_categories as $raws_category)
		@if ($raws_category->raws->isNotEmpty())
		<li>
			<span class="parent" data-open="raw_category-{{ $raws_category->id }}">{{ $raws_category->name }}</span>
			<div class="checker-nested" id="raw_category-{{ $raws_category->id }}">
				<ul class="checker">

					@foreach($raws_category->raws as $raw)
					<li class="checkbox">
						{{ Form::checkbox(null, $raw->id, in_array($raw->id, $article->raws->pluck('id')->toArray()), ['class' => 'add-raw', 'id' => 'raw-' . $raw->id]) }}
						@if(isset($raw->article))
							<label for="raw-{{ $raw->id }}">
								<span>{{ $raw->article->name }}
									@if($raw->portion_status)
										<em class="raw-portion-item">- {{ $raw->portion_count }} {{ $raw->unit_portion->abbreviation }}</em>
									@endif
								</span>
							</label>
						@endif
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
