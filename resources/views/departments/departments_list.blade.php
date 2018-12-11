@php
$drop = 1;
@endphp

{{-- Отдел --}}
<li class="medium-item item {{ isset($department->childrens) || $department->staff_count > 0 ? 'parent' : '' }}" id="departments-{{ $department->id }}" data-name="{{ $department->name }}">

    <a class="medium-link">
        <div class="icon-open sprite"></div>
        <span>{{ $department->name }}</span>
        <span class="number">{{ (isset($department->childrens) ? $department->childrens->count() : 0) + $department->staff_count }}</span>
        @moderation ($department)
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endmoderation
    </a>

    <div class="icon-list">

        <div class="controls-list">

            <div class="display-menu">
                @can ('display', $department)
                @display ($department)
                <div class="icon-display-show white sprite" data-open="item-display"></div>
                @else
                <div class="icon-display-hide white sprite" data-open="item-display"></div>
                @enddisplay
                @endcan
            </div>

            <div class="system-menu">
                {{-- Системный статус --}}
                @php
                $nested = (($department->staff_count > 0) || isset($department->childrens)) ? 1 : 0;
                @endphp
                @can ('system', $department)
                @switch($department)

                @case($department->system_item == 1 && $department->company_id == null)
                <div class="icon-system-programm white sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @break

                @case($department->system_item == null && $department->company_id == 1)
                <div class="icon-system-unlock white sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @break

                @case($department->system_item == 1 && $department->company_id == 1)
                <div class="icon-system-lock white sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @break
                @endswitch
                @endcan

                @if ($department->system_item == null && $department->company_id == null)
                <div class="icon-system-template white sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @endif
            </div>
        </div>

        <div class="actions-list">

            @can('create', $class)
            <div class="icon-list-add sprite" data-open="modal-create"></div>
            @endcan

            @can('update', $department)
            <div class="icon-list-edit sprite sprite-edit" data-open="modal-edit"></div>
            @endcan

            <div class="del">
                @can('delete', $department)
                @if(empty($department->childrens) && ($department->system_item == null) && ($department->company_id != null) && ($department->staff_count == 0))
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
        <input type="checkbox" name="" class="table-check" id="department-check-{{ $department->id }}"
        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter->booklist->booklists->default))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($department->id, $filter->booklist->booklists->default)) checked
        @endif
        @endif
        >
        <label class="label-check white" for="department-check-{{ $department->id }}"></label>
    </div>

    <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">

        @if (isset($department->childrens) || $department->staff_count > 0)

        {{-- Штат --}}
        @if ($department->staff_count > 0)
        @foreach($department->staff as $staffer)
        @include('departments.staff_list', $staffer)
        @endforeach
        @endif

        {{-- Отделы --}}
        @if (isset($department->childrens))
        @foreach($department->childrens as $department)
        @include('departments.departments_list', $department)
        @endforeach
        @endif

        @else
        <li class="empty-item"></li>
        @endif

    </ul>

</li>
















