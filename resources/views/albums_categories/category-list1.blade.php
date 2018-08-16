{{-- Шаблон вывода и динамического обновления --}}
@php
$drop = 1;
@endphp
{{-- @can('sort', App\AlbumsCategory::class)
$drop = 1;
@endcan --}}

@foreach ($items as $item)
@if($item->category_status == 1)
{{-- Если категория --}}
<li class="first-item item @if (isset($albums_categories[$item->id])) parent @endif" id="albums_categories-{{ $item->id }}" data-name="{{ $item->name }}">
  <a class="first-link @if($drop == 0) link-small @endif">
    <div class="icon-open sprite"></div>
    <span class="first-item-name">{{ $item->name }}</span>
    <span class="number">{{ $item->count }}</span>
    @if ($item->moderation)
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($item->system_item)
    <span class="system-item">Системная запись!</span>
    @endif
   
  </a>
  <div class="icon-list">

    <div class="display-menu">
      @can ('display', App\AlbumsCategory::class)
      @if ($item->display == 1)
      <div class="icon-display-show white sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide white sprite" data-open="item-display"></div>
      @endif
      @endcan
    </div>

    <div>
      @can('create', App\AlbumsCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      @can('update', $item)
      <div class="icon-list-edit sprite" data-open="first-edit"></div>
      @endcan
    </div>
    <div class="del">
      @if (!isset($item->children) && ($item->system_item != 1) && $item->delete == 1)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="check-{{ $item->id }}">
    <label class="label-check white" for="check-{{ $item->id }}"></label> 
  </div>
  <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">

     @if(isset($albums_categories[$item->id]))
        @include('albums_categories.items-list', ['items' => $albums_categories[$item->id]])

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
