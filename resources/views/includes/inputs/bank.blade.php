{{-- Банк --}}
{{ Form::text($name, ($value ?? null), ['class'=>'varchar-field', 'maxlength'=>'60', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё0-9\W\s]{3,60}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Введите банк!</span>
