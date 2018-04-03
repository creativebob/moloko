{{-- Cчет --}}
{{ Form::text($name, $value, ['class'=>'account-correspondent-field', 'maxlength'=>'20', 'autocomplete'=>'off', 'pattern'=>'[0-9]{20}', $required]) }}
<span class="form-error">Введите счет!</span>
