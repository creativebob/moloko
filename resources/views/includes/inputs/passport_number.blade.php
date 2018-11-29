{{-- Паспорт --}}
{{ Form::text($name, ($value ?? null), ['class'=>'passport-number-field', 'pattern'=>'[0-9]{2} [0-9]{2} №[0-9]{6}', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
<span class="form-error">Номер и серия паспорта</span>
