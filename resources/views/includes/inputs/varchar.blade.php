{{-- Varchar --}}
{{ Form::text($name, $value, ['class'=>'varchar-field name-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё0-9\W\s]{3,40}']) }}
<span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>