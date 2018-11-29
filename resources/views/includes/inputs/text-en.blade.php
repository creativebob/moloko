{{-- Английские буквы --}}
{{ Form::text($name,  ($value ?? null), ['class'=>'text-en-field' . (' ' . $check ?? ''), 'maxlength'=>'60', 'autocomplete'=>'off', 'pattern'=>'[A-Za-z\s-]{3,60}', ($required ?? '')]) }}
<span class="form-error">Слишком коротко!</span>
