<p class="minihead">Адрес офиса:</p>
<p class="minidecs">{{ $filial->location->city->name .', '.  $filial->location->address }}</p>
<p class="minihead">Отдел продаж:</p>
<ul class="list-phone">
	<li><span class="lab">Телефон: </span><a href="tel:{{ callPhone($filial->main_phone->phone) }}" class="call-to-phone">{{ decorPhone($filial->main_phone->phone) }}</a></li>
	<!-- <li><span class="lab">Факс: </span><a href="tel:83952211108" class="call-to-phone"></a></li> -->
</ul>
<p class="minihead">Круглосуточная техподдержка DoorHan
	(звонок по России бесплатный):
</p>
<ul class="list-phone">
	<li>
		<span class="lab">Телефон: </span>
		<a href="tel:88002009899" class="call-to-phone">8-800-200-98-99</a>
	</li>
</ul>
<p>Электронная почта: <a href="mailto:info@vorotamars.ru">info@vorotamars.ru</a></p><br>
						<!-- Skype: <a href="#">vkmars</a></p> -->