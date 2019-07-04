
<tr class="item" id="table-service-{{ $service->id }}" data-name="{{ $service->process->name }}">
	<td>

		{{ $service->category->name }}

	</td>
	<td>{{ $service->process->name }}</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('services['.$service->id.'][value]', $service->pivot ? $service->pivot->value : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder service_value', 'id'=>'1', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '', 'required']) }}
			<label for="1" class="text-to-placeholder">

				{{ $service->process->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>

	<td class="td-delete">
		@empty($disabled)
			<a class="icon-delete sprite" data-open="delete-item"></a>
		@endempty
	</td>
</tr>
