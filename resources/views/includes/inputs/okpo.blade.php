{{-- ОКПО --}}
{{ Form::text($name, $value, ['class'=>'okpo-field', 'maxlength'=>'10', 'autocomplete'=>'off', 'pattern'=>'[0-9]{10}', $required]) }}
<span class="form-error">Укажите ОКПО</span>
