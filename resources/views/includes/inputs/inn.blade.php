{{-- Инн --}}
{{ Form::text($name, ($value ?? null), ['class'=>'inn-field', 'maxlength'=>'10', 'autocomplete'=>'off', 'pattern'=>'[0-9]{10}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите ИНН</span>
