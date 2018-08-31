{{-- Телефон --}}

@php
	if(empty($id)){$id = '';};
	if(empty($singleattribute)){$singleattribute = '';};
@endphp

{{ Form::text($name, $value, ['class'=>'phone-field', 'maxlength'=>'17', 'autocomplete'=>'off', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', $required, 'id'=>$id, $singleattribute]) }}
<!-- <span class="form-error">Введите все символы телефонного номера!</span> -->
