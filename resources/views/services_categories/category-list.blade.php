{{-- Шаблон вывода и динамического обновления --}}
@php
$drop = 1;
@endphp
{{-- @can('sort', App\ServicesCategory::class)
$drop = 1;
@endcan --}}


@foreach ($services_categories_tree as $services_category)

@php
$count = 0;
@endphp
@if (isset($services_category['children']))
@php
$count = count($services_category['children']) + $count;
@endphp
@endif
@if (isset($services_category['services_products']))
@php
$count = count($services_category['services_products']) + $count;
@endphp
@endif



@if($services_category['category_status'] == 1)
{{-- Если категория --}}
<li class="first-item item @if (isset($services_category['children'])) parent @endif" id="services_categories-{{ $services_category['id'] }}" data-name="{{ $services_category['name'] }}">
  <a class="first-link @if($drop == 0) link-small @endif">
    <div class="icon-open sprite"></div>
    <span class="first-item-name">{{ $services_category['name'] }}</span>
    <span class="number">{{ $count }}</span>
    @if ($services_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
  </a>
  <div class="icon-list">
    
    <div class="display-menu">
      @can ('publisher', App\ServicesCategory::class)
      @if ($services_category['display'] == 1)
      <div class="icon-display-show white sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide white sprite" data-open="item-display"></div>
      @endif
      @endcan
    </div>

    <div>
      @can('create', App\ServicesCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      @if($services_category['edit'] == 1)
      <a class="icon-list-edit sprite" href="/services_categories/{{ $services_category['id'] }}/edit"></a>
      @endif
    </div>
    <div class="del">
      @if (empty($services_category['children']) && empty($services_category['services']) && ($services_category['system_item'] != 1) && $services_category['delete'] == 1)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="check-{{ $services_category['id'] }}">
    <label class="label-check white" for="check-{{ $services_category['id'] }}"></label> 
  </div>
  <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">

    

    @if ((isset($services_category['children'])) || ($services_category['services_products_count'] > 0))

    @if ($services_category['services_products_count'] > 0)
    @foreach($services_category['services'] as $product)
    @include('services_categories.services-list', $product)
    @endforeach
    @endif
    
    @if (isset($services_category['children']))
    @foreach($services_category['children'] as $services_category)
    @include('services_categories.services-categories-list', $services_category)
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
  if ($('#services_categories-{{ $id }}').hasClass('first-item')) {
    // Присваиваем активный класс
    $('#services_categories-{{ $id }}').addClass('first-active');
    // Открываем элемент
    $('#services_categories-{{ $id }}').children('.medium-list').addClass('is-active');
  } else {

    // Если средний элемент
    if ($('#services_categories-{{ $id }}').hasClass('medium-item')) {
      // Присваиваем элементу активный клас и открываем его и вышестоящий
      $('#services_categories-{{ $id }}').addClass('medium-active');
      $('#services_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
      $('#services_categories-{{ $id }}').children('.medium-list').addClass('is-active');
    }; 

    if ($('#services_categories-{{ $id }}').hasClass('medium-as-last')) {
      // Открываем вышестоящий
      $('#services_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
    }; 

    // Перебираем родителей
    $.each($('#services_categories-{{ $id }}').parents('.item'), function (index) {

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
