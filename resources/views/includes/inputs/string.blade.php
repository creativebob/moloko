{{-- Строка --}}
{{ Form::text($name, ($value ?? null), ['class'=>'simple-field', 'maxlength'=>'250', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9\W\s]{1,200}', (isset($required) ? 'required' : ''), 'autofocus', 'data']) }}
<span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
