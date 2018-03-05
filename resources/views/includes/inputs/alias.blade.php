{{-- Алиас --}}
{{ Form::text($name, $value, ['class'=>'alias-field', 'maxlength'=>'16', 'pattern'=>'[A-Za-z0-9-_]{16}', 'autocomplete'=>'off']) }}
