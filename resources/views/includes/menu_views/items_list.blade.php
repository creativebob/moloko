<li class="{{ isset($item->childrens) ? 'medium-item parent' : 'medium-as-last' }} item" id="{{ $entity }}-{{ $item->id }}" data-name="{{ $item->name }}">
    @if (isset($item->childrens))
    <a class="medium-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="medium-item-name">{{ $item->name }}</span>
        <span class="number">{{ count($item->childrens) }}</span>
    </a>
    @else
    <a class="medium-as-last-link">
        <span>{{ $item->name }}</span>
    </a>
    @endif

    @moderation ($item)
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endmoderation

    <div class="icon-list">
        <div class="controls-list">
            @include ('includes.control.menu-div', ['item' => $item, 'class' => $class, 'color' => 'black'])
        </div>

        <div class="actions-list">

            @can('create', $class)
            <div class="icon-list-add sprite" data-open="modal-create"></div>
            @endcan

            @can('update', $item)
            @switch($type)

            @case($type == 'modal')
            <div class="icon-list-edit sprite sprite-edit" data-open="modal-edit"></div>
            @break

            @case($type == 'edit')
            <a class="icon-list-edit sprite" href="/admin/{{ $entity }}/{{ $item->id }}/edit"></a>
            @break
            @break

            @endswitch
            @endcan

            <div class="del">
                @can('delete', $item)
                @if(empty($item->childrens) && ($item->system_item != 1))
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
        @isset($item->childrens)
        @foreach ($item->childrens as $children)
        @include('includes.menu_views.items_list', ['item' => $children])
        @endforeach
        @else
        <li class="empty-item"></li>
        @endisset
    </ul>
</li>
