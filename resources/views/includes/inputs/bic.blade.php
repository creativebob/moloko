{{-- БИК --}}
{{ Form::text($name, ($value ?? null), ['class'=>'bic-field', 'maxlength'=>'9', 'autocomplete'=>'off', 'pattern'=>'[0-9]{9}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите БИК</span>