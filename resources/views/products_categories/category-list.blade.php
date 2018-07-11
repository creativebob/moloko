{{-- Шаблон вывода и динамического обновления --}}
@php
$drop = 1;
@endphp
{{-- @can('sort', App\ProductsCategory::class)
$drop = 1;
@endcan --}}


@foreach ($products_categories_tree as $products_category)

@php
$count = 0;
@endphp
@if (isset($products_category['children']))
@php
$count = count($products_category['children']) + $count;
@endphp
@endif
@if (isset($products_category['products']))
@php
$count = count($products_category['products']) + $count;
@endphp
@endif



@if($products_category['category_status'] == 1)
{{-- Если категория --}}
<li class="first-item item @if (isset($products_category['children'])) parent @endif" id="products_categories-{{ $products_category['id'] }}" data-name="{{ $products_category['name'] }}">
  <a class="first-link @if($drop == 0) link-small @endif">
    <div class="icon-open sprite"></div>
    <span class="first-item-name">{{ $products_category['name'] }}</span>
    <span class="number">{{ $count }}</span>
    @if ($products_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
  </a>
  <div class="icon-list">
    
    <div class="display-menu">
      @can ('publisher', App\ProductsCategory::class)
      @if ($products_category['display'] == 1)
      <div class="icon-display-show white sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide white sprite" data-open="item-display"></div>
      @endif
      @endcan
    </div>

    <div>
      @can('create', App\ProductsCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      @if($products_category['edit'] == 1)
      <a class="icon-list-edit sprite" href="/products_categories/{{ $products_category['id'] }}/edit"></a>
      @endif
    </div>
    <div class="del">
      @if (empty($products_category['children']) && empty($products_category['products']) && ($products_category['system_item'] != 1) && $products_category['delete'] == 1)
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

    

    @if ((isset($products_category['children'])) || ($products_category['products_count'] > 0))

    @if ($products_category['products_count'] > 0)
    @foreach($products_category['products'] as $services_product)
    @include('products_categories.products-list', $services_product)
    @endforeach
    @endif
    
    @if (isset($products_category['children']))
    @foreach($products_category['children'] as $products_category)
    @include('products_categories.products-categories-list', $products_category)
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
  if ($('#products_categories-{{ $id }}').hasClass('first-item')) {
    // Присваиваем активный класс
    $('#products_categories-{{ $id }}').addClass('first-active');
    // Открываем элемент
    $('#products_categories-{{ $id }}').children('.medium-list').addClass('is-active');
  } else {

    // Если средний элемент
    if ($('#products_categories-{{ $id }}').hasClass('medium-item')) {
      // Присваиваем элементу активный клас и открываем его и вышестоящий
      $('#products_categories-{{ $id }}').addClass('medium-active');
      $('#products_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
      $('#products_categories-{{ $id }}').children('.medium-list').addClass('is-active');
    }; 

    if ($('#products_categories-{{ $id }}').hasClass('medium-as-last')) {
      // Открываем вышестоящий
      $('#products_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
    }; 

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
