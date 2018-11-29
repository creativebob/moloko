{{-- Varchar --}}
{{ Form::text($name, ($value ?? null), ['class'=>'varchar-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё0-9\W\s]{3,40}', (isset($required) ? 'required' : '')]) }}
<span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
