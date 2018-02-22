{{-- К/С --}}
{{ Form::text($name, $value, ['class'=>'account-correspondent-field', 'maxlength'=>'20', 'pattern'=>'[0-9]{20}', 'autocomplete'=>'off']) }}
