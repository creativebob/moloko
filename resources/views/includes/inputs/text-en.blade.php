{{-- Английские буквы --}}
{{ Form::text($name, ($value ?? null),
	[
		'class' => 'text-en-field' . (isset($check) ? ' check-field' : ''),
		'maxlength'=>'60',
		'autocomplete'=>'off',
		'pattern'=>'[A-Za-z\s-]{3,60}',
		(isset($required) ? 'required' : '')
		]) }}
		<span class="form-error">На английском!</span>
