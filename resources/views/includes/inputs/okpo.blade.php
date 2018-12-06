{{-- ОКПО --}}
{{ Form::text($name, ($value ?? null), ['class'=>'okpo-field', 'maxlength'=>'10', 'autocomplete'=>'off', 'pattern'=>'[0-9]{10}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите ОКПО</span>
