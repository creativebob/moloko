{{-- Время --}}
{{ Form::text($name, ($value ?? null), ['class'=>'time-field', 'maxlength'=>'5', 'autocomplete'=>'off', 'pattern'=>'([0-1][0-9]|[2][0-3]):[0-5][0-9]', (isset($required) ? 'required' : ''), (isset($placeholder) ? 'placeholder="10:00"' : '')]) }}
<!-- <span class="form-error">Что-то тут не так!</span> -->
