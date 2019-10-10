{{-- ОКВЭД --}}
{{ Form::text($name, ($value ?? null), ['class'=>'okved-field', 'maxlength'=>'8', 'autocomplete'=>'off', 'pattern'=>'[0-9\.]{3,8}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите ОКВЭД</span>
