{{-- Дата --}}

{{ Form::text($name, ($value ?? null), ['class'=>'date-field', 'autocomplete'=>'off', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Выберите дату!</span>
