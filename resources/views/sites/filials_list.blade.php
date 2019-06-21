<ul>
	@foreach ($filials as $filial)
	<li class="checkbox">
		{{ Form::checkbox('filials[]', $filial->id, null, ['id'=>'filial-' . $filial->id, 'class' => 'filial-checkbox']) }}
		<label for="filial-{{ $filial->id }}"><span>{{ $filial->name }}</span></label>
	</li>
	@endforeach
</ul>
