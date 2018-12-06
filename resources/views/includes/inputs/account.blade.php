{{-- Cчет --}}
{{ Form::text($name, ($value ?? null), ['class'=>'account-field', 'maxlength'=>'20', 'autocomplete'=>'off', 'pattern'=>'[0-9]{20}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Введите счет!</span>
