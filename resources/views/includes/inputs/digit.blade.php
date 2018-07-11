{{-- Количество чего-либо --}}

@php
	if(empty($pattern)){ $pattern = '[0-9\W\s]{0,7}'; }; 
	if(empty($placeholder)){ $placeholder = ''; }; 
@endphp

{{ Form::text($name, $value, ['class'=>'digit-field', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=> $pattern, $required, 'placeholder'=>$placeholder]) }}
<div class="sprite-input-right find-status" id="name-check"></div>
<span class="form-error">Введите число</span>
