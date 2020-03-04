<ul>
	@foreach ($currencies as $currency)
	<li class="checkbox">
		{{ Form::checkbox('currencies[]', $currency->id, null, ['id' => 'checkbox-currency-'.$currency->id]) }}
		<label for="checkbox-currency-{{ $currency->id }}"><span>{{ $currency->name }}</span></label>
	</li>
	@endforeach
</ul>
