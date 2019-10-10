{{-- ОГРН --}}
{{ Form::text($name, ($value ?? null), ['class'=>'ogrnip-field', 'maxlength'=>'15', 'autocomplete'=>'off', 'pattern'=>'[0-9]{13,15}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите ОГРН</span>
