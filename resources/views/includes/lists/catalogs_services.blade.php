<ul>
	@foreach ($catalogsServices as $catalogServices)
	<li class="checkbox">
		{!! Form::checkbox('catalogs_services[]', $catalogServices->id, null, ['id' => 'checkbox-catalog_services-'.$catalogServices->id]) !!}
		<label for="checkbox-catalog_services-{{ $catalogServices->id }}">
            <span>{{ $catalogServices->name }}</span>
        </label>
	</li>
	@endforeach
</ul>
