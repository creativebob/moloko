{{-- Английские буквы --}}
{{ Form::text($name,  $value, ['class'=>'text-en-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[A-Za-z\s-]{3,40}', $required]) }}