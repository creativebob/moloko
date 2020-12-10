{{-- Инн --}}
{{ Form::text('inn', ($value ?? null), ['class'=>'inn_user-field', 'maxlength'=>'10', 'autocomplete'=>'off', 'pattern'=>'[0-9]{10}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите ИНН</span>
