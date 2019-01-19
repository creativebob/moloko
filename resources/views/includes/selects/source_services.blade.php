<label>Сервис
	{!!  Form::select('source_service_id', $source_services->pluck('name', 'id'), null, [
		'id' => 'select-source_services',
		'required',
		(isset($disabled)) ? 'disabled' : '',
	]
	) !!}
</label>