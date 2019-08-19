{{-- Почтовый индекс --}}
{{ Form::text($name, ($value ?? null), ['class'=>'zip-code-field', 'maxlength'=>'6', 'autocomplete'=>'off', 'pattern'=>'[0-9]{6}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Укажите почтовый индекс</span>
