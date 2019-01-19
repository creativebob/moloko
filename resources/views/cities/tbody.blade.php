@if ($vk_cities->count > 0)

@foreach ($vk_cities->items as $item)
<tr data-tr="{{ $item->id }}">
    <td>
        <a class="city-add city-name" data-city-id="" data-city_vk_external_id="{{ $item->id }}">{{ $item->title }}</a>
    </td>
    <td>
        <a class="city-add area-name" data-area-id="" data-area-name="">{{ isset($item->area) ? $item->area : '' }}</a>
    </td>
    <td>
        <a class="city-add region-name" data-region-id="" data-region-name="">{{ isset($item->region) ? $item->region : '' }}</a>
    </td>
</tr>
@endforeach

@else

<tr>
    <td>Ничего не найдено...</td>
</tr>

@endif

