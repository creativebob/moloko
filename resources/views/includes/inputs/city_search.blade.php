@php
$id = isset($id) ? $id : '';
$city_name = isset($city) ? $city->name : null;
$city_id = isset($city) ? $city->id : null;
$field_name = isset($field_name) ? $field_name : 'city_id';
@endphp

<label id="{{ $id }}" class="city-input-parent">Город

	{{-- Город --}}
	{{ Form::text('city_name', $city_name, [
        'class' => 'varchar-field city_check-field',
        'maxlength' => '30',
        'autocomplete' => 'off',
        'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}',
        (isset($required) ? 'required' : '')
    ]
    ) }}
    <div class="sprite-input-right find-status city-check @isset ($city_name) icon-find-ok sprite-16 @endisset"></div>
    <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>

    {{-- Id города --}}
    {{ Form::hidden($field_name, $city_id, ['class'=>'city_id-field', 'maxlength'=>'3', 'pattern'=>'[0-9]{3}']) }}
    {{ Form::hidden('country_id_default', $city->country_id ?? 1) }}
</label>

<script type="application/javascript">

	{{ $id }} = new CitySearch("{{ $id }}");

	// При добавлении филиала ищем город в нашей базе
	$(document).on('keyup', '#{{ $id }} .city_check-field', function() {
		{{ $id }}.find(this);
	});

	// При клике на город в модальном окне добавления филиала заполняем инпуты
	$(document).on('click', '#{{ $id }} .city-add', function() {
		{{ $id }}.fill(this);
	});

	$(document).on('click', '#{{ $id }} .icon-find-no', function() {
		{{ $id }}.clear(this);
	});

</script>
