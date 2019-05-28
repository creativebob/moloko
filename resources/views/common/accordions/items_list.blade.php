<li class="{{ isset($item->childrens) ? 'medium-item parent' : 'medium-as-last' }} item" id="{{ $entity }}-{{ $item->id }}" data-name="{{ $item->name }}">
    @if (isset($item->childrens))
    <a class="medium-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="medium-item-name">{{ $item->name }}</span>
        <span class="number">{{ isset($item->childrens) ? $item->childrens->count() : 0 }}</span>
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
            @include ('includes.control.categories_menu_div', ['item' => $item, 'class' => $class, 'color' => 'black'])
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
            @if (isset($alias))
            {{ link_to_route($entity.'.edit', '', $parameters = ['alias' => $alias, 'id' => $item->id], $attributes = ['class' => 'icon-list-edit sprite']) }}
            @else
            {{ link_to_route($entity.'.edit', '', $parameters = ['id' => $item->id], $attributes = ['class' => 'icon-list-edit sprite']) }}
            @endif
            @break

            @endswitch
            @endcan

            <div class="del">
                @include('includes.control.item_delete_menu', ['item' => $item])
            </div>
        </div>
    </div>

    <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" id="check-{{ $item->id }}" class="check-booklist"
        @if(!empty($filter['booklist']['booklists']['default']))
        @if (in_array($item->id, $filter['booklist']['booklists']['default'])) checked
        @endif
        @endif
        >
        <label class="label-check" for="check-{{ $item->id }}"></label>
    </div>
    <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
        @isset($item->childrens)
        @foreach ($item->childrens as $children)
        @include('common.accordions.items_list', ['item' => $children])
        @endforeach
        @else
        <li class="empty-item"></li>
        @endisset
    </ul>
</li>
