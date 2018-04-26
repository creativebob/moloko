{{-- Время --}}
{{ Form::text($name, $value, ['class'=>'time-field', 'maxlength'=>'5', 'autocomplete'=>'off', 'pattern'=>'([0-1][0-9]|[2][0-3]):[0-5][0-9]', $required]) }}
<span class="form-error">Что-то тут не так!</span>
