{{-- Русские буквы --}}
{{ Form::text($name,  $value, ['class'=>'text-ru-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё\s-]{3,40}', $required]) }}
