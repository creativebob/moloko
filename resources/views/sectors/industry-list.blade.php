{{-- Шаблон вывода и динамического обновления --}}
@php
  $drop = 1;
@endphp
{{-- @can('sort', App\Sector::class)
  $drop = 1;
@endcan --}}
<ul class="vertical menu accordion-menu content-list sortable" id="content-list" data-entity="sectors" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
@foreach ($sectors_tree as $sector)
  @if($sector['industry_status'] == 1)
    {{-- Если индустрия --}}
    <li class="first-item item @if (isset($sector['children'])) parent is-accordion-submenu-parent @endif" id="sectors-{{ $sector['id'] }}" data-name="{{ $sector['sector_name'] }}">
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
      <a data-list="" class="first-link @if($drop == 0) link-small @endif">
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
      </a>
      <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" id="check-{{ $sector['id'] }}">
        <label class="label-check white" for="check-{{ $sector['id'] }}"></label> 
      </div>
    @if (isset($sector['children']))
      <ul class="menu vertical medium-list accordion-menu sortable" data-entity="sectors"  data-submenu data-accordion-menu data-allow-all-closed data-multi-open="false">
        @foreach($sector['children'] as $sector)
          @include('sectors.sectors-list', $sector)
        @endforeach
      </ul>
    @endif
    </li>
  @endif
@endforeach
</ul>


<!-- <script src="/js/vendor/foundation.js"></script> -->
<script type="text/javascript">
  $('#content-list').sortable();

  // $('#content-list').foundation();
  // Foundation.reInit($('#content-list'));

  // Foundation.reInit('entity');
  // $('#content-list').foundation();

  // $('#content-list').foundation('_destroy');
  

  @if(!empty($id))

  
  // Если первый элемент
  // if ($('#sectors-{{ $id }}').hasClass('first-item')) {
  //   // alert('Первый элемент {{ $id }}');
  //   // $('#content-list').foundation('down', $('#sectors-{{ $id }}').children('.medium-list:first'));
  //   // $('#sectors-{{ $id }}').children('.first-link:first').addClass('first-active');
  //   // $('#sectors-{{ $id }}').children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
  // } else {
  //   // alert('Средний элемент {{ $id }}');
  //   // Перебираем родителей и подсвечиваем их
  //   $.each($('#sectors-{{ $id }}').parents('.medium-list').get(), function (index) {
  //     $(this).addClass('is-active');
  //   });
  //   // $.each($('#sectors-{{ $id }}').parents('.parent').get(), function (index) {
  //   //   if ($(this).hasClass('first-item')) {
  //   //     $('#content-list').foundation('down', $(this).closest('.first-list'));
  //   //     $(this).children('.first-link:first').addClass('first-active');
  //   //   };
  //   //   if ($(this).hasClass('medium-item')) {
  //   //     $('#content-list').foundation('down', $(this).closest('.medium-list'));
  //   //     $(this).children('.medium-link:first').addClass('medium-active');
  //   //   };
  //   //   $(this).children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block'); 
  //   // });
  //   if ($('#sectors-{{ $id }}').hasClass('parent')) {
  //     alert('Средний элемент родитель {{ $id }}');
  //     $('#content-list').foundation('down', $('#sectors-{{ $id }}').children('.medium-list:first'));
  //   };
  // };    
@endif
</script>


  