{{-- Строка --}}
{{ Form::text($name, $value, ['class'=>'string-field-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9\W\s]{3,40}']) }}