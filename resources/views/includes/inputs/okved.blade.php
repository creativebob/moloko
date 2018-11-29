{{-- ОКВЭД --}}
{{ Form::text($name, ($value ?? null), ['class'=>'okved-field', 'maxlength'=>'10', 'autocomplete'=>'off', 'pattern'=>'[0-9\.]{10}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите ОКВЭД</span>
