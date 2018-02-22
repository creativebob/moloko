{{-- Дата --}}
{{ Form::text($name, $value, ['class'=>'date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off', $required]) }}
