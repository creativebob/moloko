{{-- Шаблон вывода и динамического обновления --}}
@php
$drop = 1;
@endphp
{{-- @can('sort', App\RawsCategory::class)
$drop = 1;
@endcan --}}


@foreach ($raws_categories_tree as $raws_category)

@php
$count = 0;
@endphp
@if (isset($raws_category['children']))
@php
$count = count($raws_category['children']) + $count;
@endphp
@endif



@if($raws_category['category_status'] == 1)
{{-- Если категория --}}
<li class="first-item item @if (isset($raws_category['children'])) parent @endif" id="raws_categories-{{ $raws_category['id'] }}" data-name="{{ $raws_category['name'] }}">
    <a class="first-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $raws_category['name'] }}</span>
        <span class="number">{{ $count }}</span>
        @if ($raws_category['moderation'])
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endif
    </a>
    <div class="icon-list">

        <div class="display-menu">
            @can ('publisher', App\RawsCategory::class)
            @if ($raws_category['display'] == 1)
            <div class="icon-display-show white sprite" data-open="item-display"></div>
            @else
            <div class="icon-display-hide white sprite" data-open="item-display"></div>
            @endif
            @endcan
        </div>

        <div>
            @can('create', App\RawsCategory::class)
            <div class="icon-list-add sprite" data-open="medium-add"></div>
            @endcan
        </div>
        <div>
            @if($raws_category['edit'] == 1)
            <a class="icon-list-edit sprite" href="/admin/raws_categories/{{ $raws_category['id'] }}/edit"></a>
            @endif
        </div>
        <div class="del">
            @if (empty($raws_category['children']) && empty($raws_category['raws_products']) && ($raws_category['system_item'] != 1) && $raws_category['delete'] == 1)
            <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
            @endif
        </div>
    </div>
    <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" id="check-{{ $raws_category['id'] }}">
        <label class="label-check white" for="check-{{ $raws_category['id'] }}"></label> 
    </div>
    <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">

        @if (isset($raws_category['children']))

        @if (isset($raws_category['children']))
        @foreach($raws_category['children'] as $raws_category)
        @include('raws_categories.raws-categories-list', $raws_category)
        @endforeach
        @endif

        @else
        <li class="empty-item"></li>
        @endif
    </ul>

</li>
@endif
@endforeach


{{-- Скрипт чекбоксов и перетаскивания для меню --}}
@include('includes.scripts.sortable-menu-script')

@if(!empty($id))
<script type="text/javascript">

    // Если первый элемент
    if ($('#raws_categories-{{ $id }}').hasClass('first-item')) {
        // Присваиваем активный класс
        $('#raws_categories-{{ $id }}').addClass('first-active');
        // Открываем элемент
        $('#raws_categories-{{ $id }}').children('.medium-list').addClass('is-active');
    } else {

    // Если средний элемент
    if ($('#raws_categories-{{ $id }}').hasClass('medium-item')) {
        // Присваиваем элементу активный клас и открываем его и вышестоящий
        $('#raws_categories-{{ $id }}').addClass('medium-active');
        $('#raws_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
        $('#raws_categories-{{ $id }}').children('.medium-list').addClass('is-active');
    }; 

    if ($('#raws_categories-{{ $id }}').hasClass('medium-as-last')) {
        // Открываем вышестоящий
        $('#raws_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
    }; 

    // Перебираем родителей
    $.each($('#raws_categories-{{ $id }}').parents('.item'), function (index) {

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
@endif
