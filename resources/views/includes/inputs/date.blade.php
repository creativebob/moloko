{{-- Дата --}}

{{ Form::text($name, $value, ['class'=>'date-field', 'autocomplete'=>'off', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', $required]) }}
<span class="form-error">Выберите дату!</span>
