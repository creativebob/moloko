
		@isset($clients_companies_list)
			<ul class="cell small-12">
				@foreach($clients_companies_list as $item)
						<li>{{ $item->clientable->name }}</li>
				@endforeach
			</ul>
		@endisset