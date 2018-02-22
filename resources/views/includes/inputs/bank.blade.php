{{-- Банк --}}
{{ Form::text($name, $value, ['class'=>'varchar-field bank-field', 'maxlength'=>'60', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё0-9-_/s]{3,60}']) }}
