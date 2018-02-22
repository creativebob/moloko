{{-- Адресс --}}
{{ Form::text($name, $value, ['class'=>'varchar-field address-field', 'maxlength'=>'60', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9\.\,-_\s/]{3,60}']) }}
