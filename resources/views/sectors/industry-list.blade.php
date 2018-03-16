{{-- Шаблон вывода и динамического обновления --}}
@php
  $drop = 1;
@endphp
{{-- @can('sort', App\Sector::class)
  $drop = 1;
@endcan --}}

<ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-multi-open="false" data-slide-speed="250">
@foreach ($sectors_tree as $sector)
  @if($sector['industry_status'] == 1)
    {{-- Если индустрия --}}
    <li class="first-item item @if (isset($sector['children'])) parent @endif" id="sectors-{{ $sector['id'] }}" data-name="{{ $sector['sector_name'] }}">
      
      <a data-list="" class="first-link @if($drop == 0) link-small @endif">
        <div class="drop-list checkbox">
          @if ($drop == 1)
          <div class="sprite icon-drop"></div>
          @endif
          <input type="checkbox" name="" id="check-{{ $sector['id'] }}">
          <label class="label-check white" for="check-{{ $sector['id'] }}"></label> 
        </div>
        <div class="list-title">
          <div class="icon-open sprite"></div>
          <span class="first-item-name">{{ $sector['sector_name'] }}</span>
          <span class="number">{{ $sector['count'] }}</span>
          @if ($sector['moderation'])
          <span class="no-moderation">Не отмодерированная запись!</span>
          @endif
          @if ($sector['system_item'])
          <span class="system-item">Системная запись!</span>
          @endif
        </div>
        <ul class="icon-list">
        <li>
          @can('create', App\Sector::class)
          <div class="icon-list-add sprite" data-open="medium-add"></div>
          @endcan
        </li>
        <li>
          @if($sector['edit'] == 1)
          <div class="icon-list-edit sprite" data-open="first-edit"></div>
          @endif
        </li>
        <li class="del">
          @if (!isset($sector['children']) && ($sector['system_item'] != 1) && $sector['delete'] == 1)
          <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
          @endif
        </li>
      </ul>
      </a>

    @if (isset($sector['children']))
      <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">
        @foreach($sector['children'] as $sector)
          @include('sectors.sectors-list', $sector)
        @endforeach
      </ul>
    @endif
    </li>
  @endif
@endforeach
</ul>

{{-- Скрипт чекбоксов и перетаскивания для меню --}}
@include('includes.scripts.menu-scripts')
<script src="/js/vendor/foundation.js"></script>
<script type="text/javascript">


@if(!empty($id))
$('#content-list').foundation();
  // Если первый элемент
  if ($('#sectors-{{ $id }}').hasClass('first-item')) {
    // Открываем элемент
    if ($('#sectors-{{ $id }}').hasClass('parent')) {
      $('#sectors-{{ $id }}').children('.medium-list').addClass('is-active');
    //   $('#content-list').foundation('toggle', $('#sectors-{{ $id }}').children('.medium-list:first'));
    };
    // // Присваиваем ссылке активный класс
    $('#sectors-{{ $id }}').addClass('first-active');


    // $('#sectors-{{ $id }}').attr('aria-expanded', 'true');
    // $('#sectors-{{ $id }}').children('.icon-list:first').attr('aria-hidden', 'false');
    // $('#sectors-{{ $id }}').children('.icon-list:first').css('display', 'block');



    
  };
  if ($('#sectors-{{ $id }}').hasClass('medium-item')) {
    // alert('lol');
    // $('#sectors-{{ $id }}').addClass('current-item');
    $.each($('#sectors-{{ $id }}').parents('.parent').get(), function (index) {
      if ($(this).hasClass('first-item')) {
        $(this).addClass('first-active');
      };
      if ($(this).hasClass('medium-item')) {
        $('#content-list').foundation('down', $(this).closest('.medium-list'));
        $(this).children('.medium-link:first').addClass('medium-active');
      };
      // $(this).children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block'); 
      // $(this).children('.icon-list:first').attr('aria-expanded', 'true');
    });
    $('#sectors-{{ $id }}').parents('.medium-list').addClass('is-active');
    // if ($('#sectors-{{ $id }}').hasClass('parent')) {
    //   $('#content-list').foundation('down', $('#sectors-{{ $id }}').children('.medium-list:first'));
    // };
    // alert('Средний элемент {{ $id }}');
    // Перебираем родителей и подсвечиваем их
    // $.each($('#sectors-{{ $id }}').parents('.medium-list').get(), function (index) {
    //   $(this).addClass('is-active');
    // });
      // $(this).children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block'); 
    
    
  };    
@endif
</script>


  