

@if ($attachments_categories->isNotEmpty())

@foreach($attachments_categories as $attachments_category)

@if ($attachments_category->attachments->isNotEmpty())
<li>
	<span class="parent" data-open="attachment_category-{{ $attachments_category->id }}">{{ $attachments_category->name }}</span>
	<div class="checker-nested" id="attachment_category-{{ $attachments_category->id }}">
		<ul class="checker">

			@foreach($attachments_category->attachments as $attachment)
				@if(isset($attachment->article))
					<li class="checkbox">
						{{ Form::checkbox(null, $attachment->id, in_array($attachment->id, $article->attachments->pluck('id')->toArray()), ['class' => 'add-attachment', 'id' => 'attachment-' . $attachment->id]) }}

							<label for="attachment-{{ $attachment->id }}">
								<span>{{ $attachment->article->name }}</span>
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
