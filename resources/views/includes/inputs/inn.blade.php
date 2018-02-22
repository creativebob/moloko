{{-- Инн --}}
{{ Form::text($name, $value, ['class'=>'inn-field', 'maxlength'=>'10', 'pattern'=>'[0-9]{10}', 'autocomplete'=>'off']) }}
