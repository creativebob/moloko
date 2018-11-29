{{-- Поле ввода цвета --}}
{{ Form::text($name, ($value ?? null), ['type'=>'color', 'class'=>'color-field', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Все огонь!</span>
