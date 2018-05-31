{{-- Шаблон вывода и динамического обновления --}}
@php
$drop = 1;
@endphp
{{-- @can('sort', App\AlbumsCategory::class)
$drop = 1;
@endcan --}}


@foreach ($albums_categories_tree as $albums_category)
@if($albums_category['category_status'] == 1)
{{-- Если индустрия --}}
<li class="first-item item @if (isset($albums_category['children'])) parent @endif" id="albums_categories-{{ $albums_category['id'] }}" data-name="{{ $albums_category['name'] }}">
  <a class="first-link @if($drop == 0) link-small @endif">
    <div class="icon-open sprite"></div>
    <span class="first-item-name">{{ $albums_category['name'] }}</span>
    <span class="number">{{ $albums_category['count'] }}</span>
    @if ($albums_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($albums_category['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif
    @if ($albums_category['display'] == 1)
    <span class="system-item">Отображается на сайте</span>
    @else
    <span class="no-moderation">Не отображается на сайте</span>
    @endif
  </a>
  <div class="icon-list">
    <div>
      @can('create', App\AlbumsCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      @if($albums_category['edit'] == 1)
      <div class="icon-list-edit sprite" data-open="first-edit"></div>
      @endif
    </div>
    <div class="del">
      @if (!isset($albums_category['children']) && ($albums_category['system_item'] != 1) && $albums_category['delete'] == 1)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="check-{{ $albums_category['id'] }}">
    <label class="label-check white" for="check-{{ $albums_category['id'] }}"></label> 
  </div>
  <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">
    @if (isset($albums_category['children']))
    {{-- @each('albums_categories.albums-categories-list', $sector['children'], 'sector', 'includes.empty-item') --}}
    @foreach($albums_category['children'] as $albums_category)
    @include('albums_categories.albums-categories-list', $albums_category)
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
  if ($('#albums_categories-{{ $id }}').hasClass('first-item')) {

    // Присваиваем активный класс
    $('#albums_categories-{{ $id }}').addClass('first-active');

    // Открываем элемент
    $('#albums_categories-{{ $id }}').children('.medium-list').addClass('is-active');
  } else {

    // Если средний элемент
    if ($('#albums_categories-{{ $id }}').hasClass('medium-item')) {

      // Присваиваем элементу активный клас и открываем его и вышестоящий
      $('#albums_categories-{{ $id }}').addClass('medium-active');
      $('#albums_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
      $('#albums_categories-{{ $id }}').children('.medium-list').addClass('is-active');
    };

    if ($('#albums_categories-{{ $id }}').hasClass('medium-as-last')) {

      // Открываем вышестоящий
      $('#albums_categories-{{ $id }}').parent('.medium-list').addClass('is-active');
    };
    
    // Перебираем родителей
    $.each($('#albums_categories-{{ $id }}').parents('.item'), function (index) {

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
