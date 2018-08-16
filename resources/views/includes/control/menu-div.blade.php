<div class="display-menu">
    @can ('display', $item)
    @display ($item)
    <div class="icon-display-show {{ $color }} sprite" data-open="item-display"></div>
    @else
    <div class="icon-display-hide {{ $color }} sprite" data-open="item-display"></div>
    @enddisplay
    @endcan
</div>

<div class="system-menu">
    {{-- Системный статус --}}
    @can ('system', $item)
    @switch($item)

    @case($item->system_item == 1 && $item->company_id == null)
    <div class="icon-system-programm {{ $color }} sprite" data-open="item-system"></div>
    @break

    @case($item->system_item == null && $item->company_id == 1)
    <div class="icon-system-unlock {{ $color }} sprite" data-open="item-system"></div>
    @break

    @case($item->system_item == 1 && $item->company_id == 1)
    <div class="icon-system-lock {{ $color }} sprite" data-open="item-system"></div>
    @break
    @endswitch
    @endcan

    @if ($item->system_item == null && $item->company_id == null)
    <div class="icon-system-template {{ $color }} sprite" data-open="item-system"></div>
    @endif
</div>
