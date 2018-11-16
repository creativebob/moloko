{{-- ОГРН --}}
{{ Form::text($name, $value, ['class'=>'ogrn-field', 'maxlength'=>'13', 'autocomplete'=>'off', 'pattern'=>'[0-9]{13}', $required]) }}
<span class="form-error">Укажите ОГРН</span>
