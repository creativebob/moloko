{{-- Город --}}
{{ Form::text('city_name', $city_value, ['class'=>'varchar-field city-check-field', 'autocomplete'=>'off', 'maxlength'=>'30', 'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}', $required]) }}
<div class="sprite-input-right find-status" id="city-check"></div>
<span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
{{-- Id города --}}
{{ Form::hidden('city_id', $city_id_value, ['class'=>'city-id-field', 'maxlength'=>'3', 'pattern'=>'[0-9]{3}']) }}

