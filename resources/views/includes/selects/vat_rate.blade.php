@php
	$vat_rate = array(10, 20);
@endphp

{!! Form::select('vat_rate', $vat_rate, null, ['placeholder' => 'Без НДС']) !!}

