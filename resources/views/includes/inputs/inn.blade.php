{{-- Инн --}}
{{ Form::text('inn', ($value ?? null), ['class'=>'inn-field', 'maxlength'=>'12', 'autocomplete'=>'off', 'pattern'=>'[0-9]{10,12}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите ИНН</span>
