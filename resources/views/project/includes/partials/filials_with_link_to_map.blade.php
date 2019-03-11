<p class="phone">
	<a href="tel:{{ callPhone($filial->main_phone->phone) }}">{{ decorPhone($filial->main_phone->phone) }}</a>
</p>
<p class="address">
	<a href="contacts#map" title="Смотреть на карте"><span class="geo"></span>{{ $filial->location->city->name .', '.  $filial->location->address }}</a>
</p>