{{-- ОГРН --}}
{{ Form::text($name, ($value ?? null), ['class'=>'ogrn-field', 'maxlength'=>'15', 'autocomplete'=>'off', 'pattern'=>'[0-9]{15}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите ОГРН</span>
