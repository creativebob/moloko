<ul>
	@foreach ($filials as $filial)
	<li class="checkbox">
		{{ Form::checkbox('filials[]', $filial->id, null, ['id' => 'checkbox-filial-' . $filial->id, 'class' => 'checkbox-filial']) }}
		<label for="checkbox-filial-{{ $filial->id }}"><span>{{ $filial->name }}</span></label>
	</li>
	@endforeach
</ul>
