<li class="medium-as-last item" id="staff-{{ $staffer->id }}" data-name="{{ $staffer->position->name }}">

    <div class="medium-as-last-link">
        <span>{{ $staffer->position->name }} ( {{ link_to_route('staff.edit', isset($staffer->user_id) ? $staffer->user->name : 'Вакансия', $parameters = ['id' => $staffer->id], $attributes = ['class' => 'link-recursion']) }} )</span>
        @moderation ($staffer)
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endmoderation
    </div>

    <div class="icon-list">

        <div class="controls-list">

            <div class="display-menu">
                @can ('display', $staffer)
                @display ($staffer)
                <div class="icon-display-show black sprite" data-open="item-display"></div>
                @else
                <div class="icon-display-hide black sprite" data-open="item-display"></div>
                @enddisplay
                @endcan
            </div>

            <div class="system-menu">
                {{-- Системный статус --}}
                @php
                $nested = isset($staffer->user) ? 1 : 0;
                @endphp
                @can ('system', $staffer)
                @switch($staffer)

                @case($staffer->system_item == 1 && $staffer->company_id == null)
                <div class="icon-system-programm black sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @break

                @case($staffer->system_item == null && $staffer->company_id == 1)
                <div class="icon-system-unlock black sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @break

                @case($staffer->system_item == 1 && $staffer->company_id == 1)
                <div class="icon-system-lock black sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @break
                @endswitch
                @endcan

                @if ($staffer->system_item == null && $staffer->company_id == null)
                <div class="icon-system-template black sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @endif
            </div>
        </div>

        <div class="actions-list">

            <div></div>

            @can('update', $staffer)
            <div class="icon-list-edit sprite sprite-edit">{{ link_to_route('staff.edit', '', $parameters = ['id' => $staffer->id], $attributes = []) }}</div>
            @endcan

            <div class="del">
                @can('delete', $staffer)
                @if (empty($staffer->user) && empty($staffer->system_item) && isset($staffer->company_id))
                <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
                @endif
                @endcan
            </div>

        </div>

    </div>

    <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" class="table-check" id="staffer-check-{{ $staffer->id }}"
        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter->booklist->booklists->default))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($staffer->id, $filter->booklist->booklists->default)) checked
        @endif
        @endif
        >
        <label class="label-check" for="staffer-check-{{ $staffer->id }}"></label>
    </div>

</li>