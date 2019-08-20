@isset($articles_groups)
<div class="grid-x">
<h4>Вложения:</h4>
 	@foreach($articles_groups as $articles_group)
		<div class="small-12 cell checkbox checkbox-item">
			{{ Form::checkbox('raws_articles_groups[]', $articles_group->id, isset($request->raws_articles_groups) ? in_array($articles_group->id, $request->raws_articles_groups) : null, ['id' => 'checkbox-raws_articles_groups-' .$articles_group->id]) }}
			<label for="checkbox-raws_articles_groups-{{ $articles_group->id }}"><span>{{ $articles_group->name }}</span></label>
		</div>
 	@endforeach
</div>
@endif