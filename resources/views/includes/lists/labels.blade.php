<ul>
	@foreach ($labels as $label)
	<li class="checkbox">
		{!! Form::checkbox('labels[]', $label->id, null, ['id' => 'checkbox-catalog_goods-'.$label->id]) !!}
		<label for="checkbox-catalog_goods-{{ $label->id }}">
            <span>{{ $label->name }}</span>
        </label>
	</li>
	@endforeach
</ul>
