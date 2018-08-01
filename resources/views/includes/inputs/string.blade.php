{{-- Строка --}}
{{ Form::text($name, $value, ['class'=>'string-field', 'maxlength'=>'200', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9\W\s]{1,200}', $required, 'autofocus']) }}
<span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
