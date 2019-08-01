{{-- Количество чего-либо --}}

@php
	if(empty($pattern)){ $pattern = '[0-9\W\s]{0,10}'; };
	if(empty($placeholder)){ $placeholder = ''; };
	if(empty($limit)){ $limit = 100000000; };
	if(empty($decimal_place)){ $decimal_place = 0; };
@endphp

{{ Form::text($name, ($value ?? null), [

	'class'=>'digit-field7', 
	'id'=>'digitfield-'.$name, 
	'maxlength'=>'10', 
	'autocomplete'=>'off', 
	'pattern'=> $pattern, 
	'data-limit'=> $limit, 
	'data-decimal_place'=> $decimal_place, 
	(isset($required) ? 'required' : ''), 
	'placeholder'=>$placeholder

	])
}}

<div class="sprite-input-right find-status" id="name-check"></div>
<span class="form-error">Введите число</span>

@push('scripts')
	@include('includes.scripts.class.digit-field')
@endpush