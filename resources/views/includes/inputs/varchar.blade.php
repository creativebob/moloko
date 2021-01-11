{{-- Varchar --}}
{{ Form::text($name, ($value ?? null), [
	'class'=>'varchar-field' . (isset($check) ? ' check-field' : ''),
	'maxlength'=>'120',
	'autocomplete'=>'off',
	'pattern'=>'[A-Za-zА-Яа-яЁё0-9\W\s]{3,120}',
	(isset($required) ? 'required' : '')
]
) }}
<span class="form-error">Введите хотя бы 3 символа</span>
