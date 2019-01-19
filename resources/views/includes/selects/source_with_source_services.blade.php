<div class="grid-x grid-padding-x">
	<div class="small-12 medium-6 cell">
		<label>Источник
			{!!  Form::select('source_id', $sources->pluck('name', 'id'), isset($source_service) ? $source_service->source_id : null, [
				'id' => 'select-sources',
				'required',
				(isset($disabled)) ? 'disabled' : '',
			]
			) !!}
		</label>
	</div>

	<div class="small-12 medium-6 cell" id="source_services">
		<label>Сервис
			{!!  Form::select('source_service_id', $source_services->pluck('name', 'id'), isset($source_service) ? $source_service->id : null, [
				'id' => 'select-source_services',
				'required',
				(isset($disabled)) ? 'disabled' : '',
			]
			) !!}
		</label>
	</div>
</div>