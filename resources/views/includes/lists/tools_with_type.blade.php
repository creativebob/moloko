<ul>
	@foreach ($tools as $tool)
	<li class="checkbox">
		{!! Form::checkbox('tools[]', $tool->id, null, ['id' => 'checkbox-tools-'.$tool->id]) !!}
		<label for="checkbox-tools-{{ $tool->id }}">
            <span>{{ $tool->article->name }}</span>
        </label>
	</li>
	@endforeach
</ul>
