{{-- Шаблон вывода и динамического обновления --}}
@php
$drop = 1;
@endphp
{{-- @can('sort', App\AlbumsCategory::class)
$drop = 1;
@endcan --}}

@foreach ($categories as $category)
@if($category->parent_id == null)
{{-- Если категория --}}
<li class="first-item item @isset($category->childrens) parent @endisset" id="{{ $entity }}-{{ $category->id }}" data-name="{{ $category->name }}">
    <a class="first-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $category->name }}</span>
        <span class="number">{{ isset($category->childrens) ? count($category->childrens) : 0 }}</span>

        @moderation ($category)
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endmoderation

    </a>

    <div class="icon-list">

        <div class="controls-list">

            @include ('includes.control.menu_div', ['item' => $category, 'class' => $class, 'color' => 'white'])

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

            @case($type == 'edit')
            <a class="icon-list-edit sprite" href="/admin/{{ $entity }}/{{ $category->id }}/edit"></a>
            @break
            @break

            @endswitch
            @endcan

            <div class="del">
                @can('delete', $category)
                @if(empty($category->childrens) && ($category->system_item == null) && ($category->company_id != null) && ($category->$nested == 0))
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
        <input type="checkbox" name="" id="check-{{ $category->id }}">
        <label class="label-check white" for="check-{{ $category->id }}"></label>
    </div>
    <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">
        @isset($category->childrens)
        @foreach ($category->childrens as $children)
        @include('includes.menu_views.items_list', ['item' => $children])
        @endforeach
        @else
        <li class="empty-item"></li>
        @endisset
    </ul>

</li>
@endif
@endforeach

{{-- Скрипт чекбоксов и перетаскивания для меню --}}
@include('includes.scripts.sortable-menu-script')

@isset($id)
<script type="text/javascript">

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
<script type="text/javascript">
    let count = '{{ $count }}';
    $('.content-count').text(count);
</script>
@endisset
