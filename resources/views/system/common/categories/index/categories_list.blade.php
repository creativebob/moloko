{{-- Шаблон вывода и динамического обновления --}}
@php
$drop = 1;
@endphp

@foreach ($items as $category)
@if($category->parent_id == null)
{{-- Если категория --}}
<li class="first-item item @isset($category->childrens) parent @endisset" id="{{ $entity }}-{{ $category->id }}" data-name="{{ $category->name }}">

    <a class="first-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $category->name }}</span>
        <span class="number">{{ isset($category->childrens) ? $category->childrens->count() : 0 }}</span>

        @moderation ($category)
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endmoderation

    </a>

    <div class="icon-list">

        <div class="controls-list">

            @include ('system.common.categories.index.control.categories_menu_div', [
                'item' => $category,
                'class' => $class,
                'color' => 'white'
            ]
            )

        </div>

        <div class="actions-list">

            @can('create', $class)
                <div class="icon-list-add sprite" data-open="modal-create"></div>
            @endcan

            @can('update', $category)
                @switch($type)

                    @case($type == 'modal')
                        <div class="icon-list-edit sprite sprite-edit" data-open="modal-edit"></div>
                    @break

                    @case($type == 'page')
                        <a href="{{ $entity }}/{{ $category->id }}/edit" class="icon-list-edit sprite"></a>
                        {{-- {{ link_to_route($entity.'.edit', '', ['id' => $category->id], ['class' => 'icon-list-edit sprite']) }} --}}
                    @break

                @endswitch
            @endcan

            <div class="del">
                @include('system.common.categories.index.control.item_delete_menu', ['item' => $category])
            </div>
        </div>

    </div>

    <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" id="check-{{ $category->id }}" class="check-booklist"
        @if(!empty($filter['booklist']['booklists']['default']))
        @if (in_array($category->id, $filter['booklist']['booklists']['default'])) checked
        @endif
        @endif
        >
        <label class="label-check white" for="check-{{ $category->id }}"></label>
    </div>

    <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">
        @isset($category->childrens)
        @foreach ($category->childrens as $children)
        @include('system.common.categories.index.items_list', ['item' => $children])
        @endforeach
        @else
        <li class="empty-item"></li>
        @endisset
    </ul>

</li>
@endif
@endforeach

{{-- Скрипт чекбоксов и перетаскивания для меню --}}
{{--@include('includes.scripts.sortable-menu-script')--}}

@isset($id)
<script type="application/javascript">

    // Если первый элемент
    if ($('#{{ $entity }}-{{ $id }}').hasClass('first-item')) {

        // Присваиваем активный класс
        $('#{{ $entity }}-{{ $id }}').addClass('first-active');

        // Открываем элемент
        $('#{{ $entity }}-{{ $id }}').children('.medium-list').addClass('is-active');

    } else {

        // Если средний элемент
        if ($('#{{ $entity }}-{{ $id }}').hasClass('medium-item')) {

            // Присваиваем элементу активный клас и открываем его и вышестоящий
            $('#{{ $entity }}-{{ $id }}').addClass('medium-active');
            $('#{{ $entity }}-{{ $id }}').parent('.medium-list').addClass('is-active');
            $('#{{ $entity }}-{{ $id }}').children('.medium-list').addClass('is-active');

        };

        if ($('#{{ $entity }}-{{ $id }}').hasClass('medium-as-last')) {

            // Открываем вышестоящий
            $('#{{ $entity }}-{{ $id }}').parent('.medium-list').addClass('is-active');
        };

        // Перебираем родителей
        $.each($('#{{ $entity }}-{{ $id }}').parents('.item'), function (index) {

            // Если первый элемент, присваиваем активный класс
            if ($(this).hasClass('first-item')) {
                $(this).addClass('first-active');
            };

            // Если средний элемент, присваиваем активный класс
            if ($(this).hasClass('medium-item')) {
                $(this).addClass('medium-active');
                $(this).parent('.medium-list').addClass('is-active');
            };
        });
    };
</script>
@endisset

@isset ($count)
<script type="application/javascript">
    $('.content-count').text('{{ num_format($count, 0) }}');
</script>
@endisset
