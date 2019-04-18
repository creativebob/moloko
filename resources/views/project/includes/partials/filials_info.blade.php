<span itemprop="addressLocality">{{ $filial->location->city->name }}</span>,
<span itemprop="streetAddress">{{ $filial->location->address }}<br>
	<span itemprop="email">info@vorotamars.ru</span><br>
	<span itemprop="telephone">{{ decorPhone($filial->main_phone->phone) }}</span>
</span>