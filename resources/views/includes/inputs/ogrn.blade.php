{{-- ОГРН --}}
{{ Form::text($name, ($value ?? null), ['class'=>'ogrn-field', 'maxlength'=>'13', 'autocomplete'=>'off', 'pattern'=>'[0-9]{13}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите ОГРН</span>
