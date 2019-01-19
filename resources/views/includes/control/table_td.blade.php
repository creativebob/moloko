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
        @php
            if (isset($nested)) {
                $nested_count = ($item->$nested > 0) ? 1 : 0;
            } else {
                $nested_count = 0;
            }
        @endphp

        @switch($item)

        @case($item->system_item == 1 && $item->company_id == null)
        <div class="icon-system-programm black sprite" data-open="item-system" data-nested="{{ $nested_count }}"></div>
        @break

        @case($item->system_item == null && $item->company_id == 1)
        <div class="icon-system-unlock black sprite" data-open="item-system" data-nested="{{ $nested_count }}"></div>
        @break

        @case($item->system_item == 1 && $item->company_id == 1)
        <div class="icon-system-lock black sprite" data-open="item-system" data-nested="{{ $nested_count }}"></div>
        @break
        @endswitch
        @endcan

        @if ($item->system_item == null && $item->company_id == null)
        <div class="icon-system-template black sprite" data-open="item-system" data-nested="{{ $nested_count }}"></div>
        @endif

</td>
