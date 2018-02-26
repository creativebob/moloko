{{-- Телефон --}}
{{ Form::text($name, $value, ['class'=>'phone-field', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'maxlength'=>'17', 'autocomplete'=>'off', $required]) }}
<span class="form-error">Введите все символы телефонного номера!</span>
