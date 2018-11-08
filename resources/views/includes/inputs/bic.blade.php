{{-- БИК --}}
{{ Form::text($name, $value, ['class'=>'bic-field', 'maxlength'=>'9', 'autocomplete'=>'off', 'pattern'=>'[0-9]{9}', $required]) }}
<span class="form-error">Укажите БИК</span>