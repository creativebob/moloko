{{-- Английские буквы --}}
{{ Form::text($name, ($value ?? null),
	[
		'class' => 'text-en-field' . (isset($check) ? ' check-field' : ''),
		'maxlength'=>'200',
		'autocomplete'=>'off',
		'pattern'=>'[A-Za-z\s-_?&=%]{1,200}',
		(isset($required) ? 'required' : '')
		]) }}
		<span class="form-error">На английском!</span>
