{{-- Поле для хранения ссылок --}}
{{ Form::text($name, $value, ['class'=>'link-field', 'maxlength'=>'100', 'autocomplete'=>'off', 'pattern'=>'[A-Za-z0-9-_]{6,100}', $required]) }}
<span class="form-error">Странная ссылка ;)</span>
