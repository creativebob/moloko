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

    @php
    if (isset($nested)) {
        $nested_count = ($item->staff_count > 0) || isset($item->childrens) ? 1 : 0;
    } else {
        $nested_count = 0;
    }
    @endphp
    @can ('system', $item)
    @switch($item)

    @case($item->system == 1 && $item->company_id == null)
    <div class="icon-system-programm {{ $color }} sprite" data-open="item-system" data-nested="{{ $nested_count }}"></div>
    @break

    @case($item->system == null && $item->company_id == 1)
    <div class="icon-system-unlock {{ $color }} sprite" data-open="item-system" data-nested="{{ $nested_count }}"></div>
    @break

    @case($item->system == 1 && $item->company_id == 1)
    <div class="icon-system-lock {{ $color }} sprite" data-open="item-system" data-nested="{{ $nested_count }}"></div>
    @break
    @endswitch
    @endcan

    @if ($item->system == null && $item->company_id == null)
    <div class="icon-system-template {{ $color }} sprite" data-open="item-system" data-nested="{{ $nested_count }}"></div>
    @endif
</div>
