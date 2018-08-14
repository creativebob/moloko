<td class="td-control">

    {{-- Отображение на сайте --}}
    @can ('display', $item)
    @display ($item)
    <a class="icon-display-show black sprite" data-open="item-display"></a>
    @else
    <a class="icon-display-hide black sprite" data-open="item-display"></a>
    @enddisplay
    @endcan

    {{-- Системный статус --}}
    @can ('system', $item)
    @switch($item)

    @case($item->system_item == 1 && $item->company_id == null)
    <a class="icon-system-programm sprite" data-open="item-system"></a>
    @break

    @case($item->system_item == null && $item->company_id == 1)
    <a class="icon-system-unlock sprite" data-open="item-system"></a>
    @break

    @case($item->system_item == 1 && $item->company_id == 1)
    <a class="icon-system-lock sprite" data-open="item-system"></a>
    @break
    @endswitch
    @endcan

    @if ($item->system_item == null && $item->company_id == null)
    <a class="icon-system-template sprite" data-open="item-system"></a>
    @endif

</td>
