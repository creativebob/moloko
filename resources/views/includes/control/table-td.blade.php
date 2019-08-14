<td class="td-control">

    {{-- Отображение на сайте --}}
    @can ('display', $item)
    @display ($item)
    <div class="icon-display-show black sprite" data-open="item-display"></div>
    @else
    <div class="icon-display-hide black sprite" data-open="item-display"></div>
    @enddisplay
    @endcan

    {{-- Системный статус --}}
    @can ('system', $item)
    @switch($item)

    @case($item->system == 1 && $item->company_id == null)
    <div class="icon-system-programm black sprite" data-open="item-system"></div>
    @break

    @case($item->system == null && $item->company_id == 1)
    <div class="icon-system-unlock black sprite" data-open="item-system"></div>
    @break

    @case($item->system == 1 && $item->company_id == 1)
    <div class="icon-system-lock black sprite" data-open="item-system"></div>
    @break
    @endswitch
    @endcan

    @if ($item->system == null && $item->company_id == null)
    <div class="icon-system-template black sprite" data-open="item-system"></div>
    @endif

</td>
