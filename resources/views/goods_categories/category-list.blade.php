{{-- Шаблон вывода и динамического обновления --}}
@php
$drop = 1;
@endphp
{{-- @can('sort', App\GoodsCategory::class)
$drop = 1;
@endcan --}}


@foreach ($goods_categories_tree as $goods_category)

@php
$count = 0;
@endphp
@if (isset($goods_category['children']))
@php
$count = count($goods_category['children']) + $count;
@endphp
@endif
@if (isset($goods_category['goods_products']))
@php
$count = count($goods_category['goods_products']) + $count;
@endphp
@endif



@if($goods_category['category_status'] == 1)
{{-- Если категория --}}
<li class="first-item item @if (isset($goods_category['children'])) parent @endif" id="goods_categories-{{ $goods_category['id'] }}" data-name="{{ $goods_category['name'] }}">
    <a class="first-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $goods_category['name'] }}</span>
        <span class="number">{{ $count }}</span>
        @if ($goods_category['moderation'])
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endif
    </a>
    <div class="icon-list">

        <div class="display-menu">
            @can ('publisher', App\GoodsCategory::class)
            @if ($goods_category['display'] == 1)
            <div class="icon-display-show white sprite" data-open="item-display"></div>
            @else
            <div class="icon-display-hide white sprite" data-open="item-display"></div>
            @endif
            @endcan
        </div>

        <div>
            @can('create', App\GoodsCategory::class)
            <div class="icon-list-add sprite" data-open="medium-add"></div>
            @endcan
        </div>
        <div>
            @if($goods_category['edit'] == 1)
            <a class="icon-list-edit sprite" href="/admin/goods_categories/{{ $goods_category['id'] }}/edit"></a>
            @endif
        </div>
        <div class="del">
            @if (empty($goods_category['children']) && empty($goods_category['goods']) && ($goods_category['system_item'] != 1) && $goods_category['delete'] == 1)
            <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
            @endif
        </div>
    </div>
    <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" id="check-{{ $goods_category['id'] }}">
        <label class="label-check white" for="check-{{ $goods_category['id'] }}"></label> 
    </div>
    <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">



        @if ((isset($goods_category['children'])) || ($goods_category['goods_products_count'] > 0))

        @if ($goods_category['goods_products_count'] > 0)
        @foreach($goods_category['goods_products'] as $product)
        @include('goods_categories.goods-products-list', $product)
        @endforeach
        @endif

        @if (isset($goods_category['children']))
        @foreach($goods_category['children'] as $goods_category)
        @include('goods_categories.goods-categories-list', $goods_category)
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
    if ($('#goods_categories-{{ $id }}').hasClass('first-item')) {
        // Присваиваем активный класс
        $('#goods_categories-{{ $id }}').addClass('first-active');
        // Открываем элемент
        $('#goods_categories-{{ $id }}').children('.medium-list').addClass('is-active');
    } else {

    // Если средний элемент
    if ($('#goods_categories-{{ $id }}').hasClass('medium-item')) {
        // Присваиваем элементу активный клас и открываем его и вышестоящий
        $('#goods_categories-{{ $id }}').addClass('medium-active');
        $('#goods_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
        $('#goods_categories-{{ $id }}').children('.medium-list').addClass('is-active');
    }; 

    if ($('#goods_categories-{{ $id }}').hasClass('medium-as-last')) {
        // Открываем вышестоящий
        $('#goods_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
    }; 

    // Перебираем родителей
    $.each($('#goods_categories-{{ $id }}').parents('.item'), function (index) {

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
