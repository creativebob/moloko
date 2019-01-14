<div class="small-12 cell">
	<label>Название:
		@include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required' => true])
	</label>
</div>
<div class="small-12 cell">
	<label>Описание:
		@include('includes.inputs.textarea', ['value'=>null, 'name'=>'description'])
	</label>
</div>

@switch($type)

@case('numeric')
<div class="small-6 cell">
	<label>Минимум
		{{ Form::number('min') }}
	</label>
</div>
<div class="small-6 cell">
	<label>Максимум
		{{ Form::number('max') }}
	</label>
</div>
<div class="small-6 cell">
	<label>Единица измерения
		{{ Form::select('unit_id', $units_list, null) }}
	</label>
</div>
<div class="small-6 cell">
	<label>Знаки после запятой
		{{ Form::select('decimal_place', ['0' => '0', '1' => '0,0', '2' => '0,00', '3' => '0,000'], null) }}
	</label>
</div>

{{ Form::hidden('type', 'numeric') }}
@break

@case('percent')
<div class="small-6 cell">
	<label>Минимум
		{{ Form::number('min') }}
	</label>
</div>

<div class="small-6 cell">
	<label>Максимум
		{{ Form::number('max') }}
	</label>
</div>

<div class="small-6 cell">
	<label>Единица измерения
		{{ Form::select('unit_id', $units_list, null) }}
	</label>
</div>

<div class="small-6 cell">
	<label>Знаки после запятой
		{{ Form::select('decimal_place', ['0' => '0', '1' => '0,0', '2' => '0,00', '3' => '0,000'], null) }}
	</label>
</div>

{{ Form::hidden('type', 'percent') }}
@break

@case('list')

<div class="small-12 cell">
	<div class="radiobutton">
		{{ Form::radio('list_type', 'list', true, ['id' => $set_status.'-metric-list-type']) }}
		<label for="{{ $set_status }}-metric-list-type"><span>Выбор нескольких значений</span></label>
		{{ Form::radio('list_type', 'select', false, ['id' => $set_status.'-metric-select-type']) }}
		<label for="{{ $set_status }}-metric-select-type"><span>Выбор одного значения</span></label>
	</div>
</div>



<div class="input-group inputs small-12 cell">

		{{ Form::text('value', null, ['id' => $set_status.'-value', 'placeholder' => 'Введите значение']) }}


	  <div class="input-group-button">
		<a class="button add-value">Добавить в список</a>
	  </div>
</div>


<table id="{{ $set_status }}-values-table" class="tablesorter">
	<tbody id="{{ $set_status }}-values-tbody">

	</tbody>
</table>
{{ Form::hidden('type', 'list') }}

@break

@endswitch

{{ Form::hidden('property_id', $property_id) }}
{{ Form::hidden('entity', $entity) }}

<div class="small-12 cell text-center">
	<a class="button green-button" id="add-metric">Создать метрику</a>
</div>



