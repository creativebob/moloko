{{-- Инн --}}
{{ Form::text($name, $value, ['class'=>'inn-field', 'maxlength'=>'10', 'autocomplete'=>'off', 'pattern'=>'[0-9]{10}', $required]) }}
<span class="form-error">Укажите ИНН</span>
