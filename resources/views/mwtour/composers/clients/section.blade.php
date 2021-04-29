@if ($clients_companies_list->isNotEmpty())
<section>
	<h2 class="h2">Нам доверяют</h2>
    <ul class="grid-x grid-padding-x small-up-3 medium-up-4 large-up-5 align-center list-clients">
        @foreach($clients_companies_list as $client)
            <li class="cell clinets-companies-item" data-equalizer-watch>
                <img src="{{ getPhotoPath($client->clientable, 'small') }}" 
                alt="{{ $client->clientable->name ?? '' }}" width="150" height="99">
            </li>
        @endforeach
    </ul>
</section>
@endif
