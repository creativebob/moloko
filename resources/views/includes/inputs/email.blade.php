{{-- Почта --}}
{{ Form::email($name, $value, ['class'=>'email-field', 'maxlength'=>'30', 'autocomplete'=>'off', $required]) }}
<span class="form-error">Укажите почту</span>
