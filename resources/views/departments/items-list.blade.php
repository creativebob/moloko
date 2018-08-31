{{-- Если вложенный --}}

@foreach ($items as $item)

@php
$count = 0;

if (isset($grouped_items[$item->id])) {
    $count = $count + count($grouped_items[$item->id]);
};
if (isset($item->staff)) {
    $count = $count + count($item->staff);
};
@endphp

@if ((isset($grouped_items[$item->id])) || (count($item->staff) > 0))
<li class="medium-item item parent" id="{{ $entity }}-{{ $item->id }}" data-name="{{ $item->name }}">
    <a class="medium-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="medium-item-name">{{ $item->name }}</span>
        <span class="number">{{ $count }}</span>
        @moderation ($item)
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endmoderation

    </a>
    <div class="icon-list">

        <div class="controls-list">

            @include ('includes.control.menu-div', ['item' => $item, 'class' => $class, 'color' => 'black'])

        </div>

        <div class="actions-list">

            @can('create', $class)
            <div class="icon-list-add sprite" data-open="medium-add"></div>
            @endcan

            @can('update', $item)
            @switch($type)

            @case($type == 'modal')
            <div class="icon-list-edit sprite" data-open="medium-edit"></div>
            @break

            @case($type == 'edit')
            <a class="icon-list-edit sprite" href="/admin/{{ $entity }}/{{ $category['id'] }}/edit"></a>
            @break
            @break

            @endswitch
            @endcan

            <div class="del">
                @can('delete', $item)
                @if(empty($grouped_items[$item->id]) && ($item->system_item != 1))
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
        <input type="checkbox" name="" id="check-{{ $item->id }}">
        <label class="label-check" for="check-{{ $item->id }}"></label> 
    </div>
    <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
        @if ((isset($grouped_items[$item->id])) || (count($item->staff) > 0))

        @if(isset($grouped_items[$item->id]))
        @include('departments.items-list', ['grouped_items' => $grouped_items, 'items' => $grouped_items[$item->id], 'class' => $class, 'entity' => $entity, 'type' => $type])
        @endif

        @if(count($item->staff) > 0)
        @foreach ($item->staff as $staffer)
        @include('departments.staff-list', ['grouped_items' => $grouped_items, 'staffer' => $staffer, 'class' => 'App\Staffer', 'entity' => 'staff', 'type' => $type])
        @endforeach
        @endif

        @else
        <li class="empty-item"></li>
        @endif
    </ul>
</li>
@else

{{-- Конечный --}}
<li class="medium-as-last item" id="{{ $entity }}-{{ $item->id }}" data-name="{{ $item->name }}">
    <a class="medium-as-last-link">
        <span>{{ $item->name }}</span>
        @moderation ($item)
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endmoderation
    </a>
    <div class="icon-list">

        <div class="controls-list">

            @include ('includes.control.menu-div', ['item' => $item, 'class' => $class, 'color' => 'black'])

        </div>

        <div class="actions-list">

            @can('create', $class)
            <div class="icon-list-add sprite" data-open="medium-add"></div>
            @endcan

            @can('update', $item)
            @switch($type)

            @case($type == 'modal')
            <div class="icon-list-edit sprite" data-open="medium-edit"></div>
            @break

            @case($type == 'edit')
            <a class="icon-list-edit sprite" href="/admin/{{ $entity }}/{{ $category['id'] }}/edit"></a>
            @break
            @break

            @endswitch
            @endcan

            <div class="del">
                @can('delete', $item)
                @if($item->system_item != 1)
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
        <input type="checkbox" name="" id="item-check-{{ $item->id }}">
        <label class="label-check" for="item-check-{{ $item->id }}"></label> 
    </div>
</li>
@endif
@endforeach
















