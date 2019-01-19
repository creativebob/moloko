<table class="content-table-search table-over">
	<tbody>

		@forelse ($cities as $city)
		<tr data-tr="{{ $city->id }}" data-city-id="{{ $city->id }}">
			<td>
				<a class="city-add city-name">{{ $city->name }}</a>
			</td>
			<td>
				<a class="city-add">{{ $city->area->name ?? '' }}</a>
			</td>
			<td>
				<a class="city-add">{{ $city->region->name ?? $city->area->region->name }}</a>
			</td>
		</tr>
		@empty
		<tr class="no-city">
			<td>Населенный пункт не найден в базе данных,

				@can('create', App\City::class)
				<a href="/admin/cities" target="_blank">добавьте его!</a>
				@endcan

				@cannot('create', App\City::class)
				обратитесь к администратору!
				@endcannot

			</td>
		</tr>
		@endforelse

	</tbody>
</table>