{{-- Русские буквы --}}
{{ Form::text($name,  $value, ['class'=>'text-ru-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё\s-]{1,40}', $required]) }}
<span class="form-error"></span>
