{{-- Кпп --}}
{{ Form::text($name, $value, ['class'=>'kpp-field', 'maxlength'=>'9', 'pattern'=>'[0-9]{9}', 'autocomplete'=>'off', $required]) }}
<span class="form-error">Укажите КПП</span>
