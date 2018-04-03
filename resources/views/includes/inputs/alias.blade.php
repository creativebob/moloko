{{-- Алиас --}}
{{ Form::text($name, $value, ['class'=>'alias-field', 'maxlength'=>'16', 'autocomplete'=>'off', 'pattern'=>'[A-Za-z0-9-_]{3,16}', $required]) }}
<span class="form-error">Введите алиас!</span>
