{{-- Строка --}}
{{ Form::text($name, $value, ['class'=>'string-field', 'maxlength'=>'45', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9\W\s]{1,45}', $required]) }}
<span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
