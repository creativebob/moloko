{{-- Шаблон вывода и динамического обновления --}}
@php
  $drop = 1;
@endphp
{{-- @can('sort', App\ProductsCategory::class)
  $drop = 1;
@endcan --}}


@foreach ($products_categories_tree as $products_category)
  @if($products_category['category_status'] == 1)
    {{-- Если индустрия --}}
    <li class="first-item item @if (isset($products_category['children'])) parent @endif" id="products_categories-{{ $products_category['id'] }}" data-name="{{ $products_category['name'] }}">
      <a class="first-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $products_category['name'] }}</span>
        <span class="number">{{ $products_category['count'] }}</span>
        @if ($products_category['moderation'])
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endif
        @if ($products_category['system_item'])
        <span class="system-item">Системная запись!</span>
        @endif
      </a>
      <div class="icon-list">
        <div>
          @can('create', App\ProductsCategory::class)
          <div class="icon-list-add sprite" data-open="medium-add"></div>
          @endcan
        </div>
        <div>
          @if($products_category['edit'] == 1)
          <div class="icon-list-edit sprite" data-open="first-edit"></div>
          @endif
        </div>
        <div class="del">
          @if (!isset($products_category['children']) && ($products_category['system_item'] != 1) && $products_category['delete'] == 1)
          <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
          @endif
        </div>
      </div>
      <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" id="check-{{ $products_category['id'] }}">
        <label class="label-check white" for="check-{{ $products_category['id'] }}"></label> 
      </div>
      <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">
        @if (isset($products_category['children']))
        {{-- @each('products_categories.products-categories-list', $sector['children'], 'sector', 'includes.empty-item') --}}
        @foreach($products_category['children'] as $products_category)
          @include('products_categories.products-categories-list', $products_category)
        @endforeach
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
  if ($('#products_categories-{{ $id }}').hasClass('first-item')) {
    // Присваиваем активный класс
    $('#products_categories-{{ $id }}').addClass('first-active');
    // Открываем элемент
    $('#products_categories-{{ $id }}').children('.medium-list').addClass('is-active');
  };

  // Если средний элемент
  if ($('#products_categories-{{ $id }}').hasClass('medium-item')) {
    // Присваиваем элементу активный клас и открываем его и вышестоящий
    $('#products_categories-{{ $id }}').addClass('medium-active');
    $('#products_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
    $('#products_categories-{{ $id }}').children('.medium-list').addClass('is-active');

    // Перебираем родителей
    $.each($('#products_categories-{{ $id }}').parents('.item'), function (index) {

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
  