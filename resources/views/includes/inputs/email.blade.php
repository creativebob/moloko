{{-- Почта --}}
{{ Form::email($name, $value, ['class'=>'email-field', 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$', $required]) }}
<span class="form-error">Укажите почту</span>



