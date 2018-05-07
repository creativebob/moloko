@php
  $drop = 1;
@endphp
{{-- @can('sort', App\Sector::class)
  $drop = 1;
@endcan --}}

<ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250">
  @foreach ($navigations_tree as $navigation)
  {{-- Если Подкатегория --}}
  <li class="first-item item @if (isset($navigation['menus'])) parent @endif" id="navigations-{{ $navigation['id'] }}" data-name="{{ $navigation['name'] }}">
    <a class="first-link @if($drop == 0) link-small @endif">
      <div class="icon-open sprite"></div>
      <span class="first-item-name">{{ $navigation['name'] }}</span>
      <span class="number">
      @if (isset($navigation['menus']))
        {{ count($navigation['menus']) }}
      @else
        0
      @endif
      </span>
      @if ($navigation['moderation'])
      <span class="no-moderation">Не отмодерированная запись!</span>
      @endif
      @if ($navigation['system_item'])
      <span class="system-item">Системная запись!</span>
      @endif
    </a>
    <div class="icon-list">
      <div>
        @can('create', App\Menu::class)
        <div class="icon-list-add sprite" data-open="medium-add"></div>
        @endcan
      </div>
      <div>
        @if($navigation['edit'] == 1)
        <div class="icon-list-edit sprite" data-open="first-edit"></div>
        @endif
      </div>
      <div class="del">
        @if(($navigation['system_item'] != 1) && (!isset($navigation['menus'])) && ($navigation['delete'] == 1))
        <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
        @endif
      </div>
    </div>
    <div class="drop-list checkbox">
      @if ($drop == 1)
      <div class="sprite icon-drop"></div>
      @endif
      <input type="checkbox" name="" id="navigation-check-{{ $navigation['id'] }}">
      <label class="label-check white" for="navigation-check-{{ $navigation['id'] }}"></label> 
    </div>
    <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">
    @if (isset($navigation['menus']))
      @foreach($navigation['menus'] as $menu)
        @include('navigations.menus-list', $menu)
      @endforeach
    @else
      <li class="empty-item"></li>
    @endif
    </ul>
  </li>
  @endforeach
</ul>

{{-- Скрипт чекбоксов и перетаскивания для меню --}}
@include('includes.scripts.menu-scripts')

@if(!empty($id))
<script type="text/javascript">

  if ('{{ $item }}' == 'navigation') {

    // Если первый элемент
    if ($('#navigations-{{ $id }}').hasClass('first-item')) {
      // Присваиваем активный класс
      $('#navigations-{{ $id }}').addClass('first-active');
      // Открываем элемент
      $('#navigations-{{ $id }}').children('.medium-list').addClass('is-active');
    };
  };

  if ('{{ $item }}' == 'menu') {
    // Если средний элемент
    if ($('#menus-{{ $id }}').hasClass('medium-item')) {
      // Присваиваем элементу активный клас и открываем его и вышестоящий
      $('#menus-{{ $id }}').addClass('medium-active');
      $('#menus-{{ $id }}').parent('.medium-list').addClass('is-active');
      $('#menus-{{ $id }}').children('.medium-list').addClass('is-active');
    };
    if ($('#menus-{{ $id }}').hasClass('medium-as-last')) {
      // Открываем вышестоящий
      $('#menus-{{ $id }}').parent('.medium-list').addClass('is-active');
    };

    // Перебираем родителей
    $.each($('#menus-{{ $id }}').parents('.item'), function (index) {

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