<td class="td-control">

    @isset($replicate)
        <div title="Клонировать" class="icon-clone black sprite" data-open="modal-replicate"></div>
    @endisset

    @isset($appointment)
        <div title="Назначить" class="icon-clone black sprite" data-open="modal-appointment"></div>
    @endisset

    {{-- Отображение на сайте --}}
    @can('display', $item)
        @display($item)
        <div class="icon-display-show black sprite" data-open="item-display"></div>
    @else
        <div class="icon-display-hide black sprite" data-open="item-display"></div>
        @enddisplay
    @endcan

    @php
        if (isset($nested)) {
            $nested_count = ($item->$nested > 0) ? 1 : 0;
        } else {
            $nested_count = 0;
        }
    @endphp

    {{-- Системный статус --}}
    @can ('system', $item)

        @switch($item)

            @case($item->system == 1 && $item->company_id == null)
            <div title="Системная запись" class="icon-system-programm black sprite" data-open="item-system"
                 data-nested="{{ $nested_count }}"></div>
            @break

            @case($item->system == null && $item->company_id == 1)
            <div class="icon-system-unlock black sprite" data-open="item-system"
                 data-nested="{{ $nested_count }}"></div>
            @break

            @case($item->system == 1 && $item->company_id == 1)
            <div class="icon-system-lock black sprite" data-open="item-system" data-nested="{{ $nested_count }}"></div>
            @break
        @endswitch
    @endcan

    @if ($item->system == null && $item->company_id == null)
        <div title="Шаблон" class="icon-system-template black sprite" data-open="item-system"
             data-nested="{{ $nested_count }}"></div>
    @endif

</td>
