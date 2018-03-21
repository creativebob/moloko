{{-- Шаблон вывода и динамического обновления --}}
@php
  $drop = 1;
@endphp
{{-- @can('sort', App\Sector::class)
  $drop = 1;
@endcan --}}

<ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250">
@foreach ($sectors_tree as $sector)
  @if($sector['industry_status'] == 1)
    {{-- Если индустрия --}}
    <li class="first-item item @if (isset($sector['children'])) parent @endif" id="sectors-{{ $sector['id'] }}-content" data-name="{{ $sector['sector_name'] }}">
      <a class="first-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $sector['sector_name'] }}</span>
        <span class="number">{{ $sector['count'] }}</span>
        @if ($sector['moderation'])
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endif
        @if ($sector['system_item'])
        <span class="system-item">Системная запись!</span>
        @endif
      </a>
      <div class="icon-list">
        <div>
          @can('create', App\Sector::class)
          <div class="icon-list-add sprite" data-open="medium-add"></div>
          @endcan
        </div>
        <div>
          @if($sector['edit'] == 1)
          <div class="icon-list-edit sprite" data-open="first-edit"></div>
          @endif
        </div>
        <div class="del">
          @if (!isset($sector['children']) && ($sector['system_item'] != 1) && $sector['delete'] == 1)
          <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
          @endif
        </div>
      </div>
      <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" id="check-{{ $sector['id'] }}">
        <label class="label-check white" for="check-{{ $sector['id'] }}"></label> 
      </div>
      <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">
        @if (isset($sector['children']))
        {{-- @each('sectors.sectors-list', $sector['children'], 'sector', 'includes.empty-item') --}}
        @foreach($sector['children'] as $sector)
          @include('sectors.sectors-list', $sector)
        @endforeach
        @else
          <li class="empty-item"></li>
        @endif
      </ul>
    
    </li>
  @endif
@endforeach
</ul>

{{-- Скрипт чекбоксов и перетаскивания для меню --}}
@include('includes.scripts.menu-scripts')

@if(!empty($id))
<script type="text/javascript">

  // Если первый элемент
  if ($('#sectors-{{ $id }}-content').hasClass('first-item')) {
    // Присваиваем активный класс
    $('#sectors-{{ $id }}-content').addClass('first-active');
    // Открываем элемент
    $('#sectors-{{ $id }}-content').children('.medium-list').addClass('is-active');
  };

  // Если средний элемент
  if ($('#sectors-{{ $id }}-content').hasClass('medium-item')) {
    // Присваиваем элементу активный клас и открываем его и вышестоящий
    $('#sectors-{{ $id }}-content').addClass('medium-active');
    $('#sectors-{{ $id }}-content').parent('.medium-list').addClass('is-active');
    $('#sectors-{{ $id }}-content').children('.medium-list').addClass('is-active');

    // Перебираем родителей
    $.each($('#sectors-{{ $id }}-content').parents('.item'), function (index) {

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
  