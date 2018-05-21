@php
$drop = 1;
@endphp
{{-- @can('sort', App\Sector::class)
$drop = 1;
@endcan --}}


  @foreach ($departments_tree as $department)
  @if($department['filial_status'] == 1)
  {{-- Если филиал --}}
  <li class="first-item item @if (isset($department['children']) || isset($department['staff'])) parent @endif" id="departments-{{ $department['id'] }}" data-name="{{ $department['name'] }}">
      <a class="first-link @if($drop == 0) link-small @endif">
          <div class="icon-open sprite"></div>
          <span class="first-item-name">{{ $department['name'] }}</span>
          <span class="number">{{ $department['count'] }}</span>
          @if ($department['moderation'])
          <span class="no-moderation">Не отмодерированная запись!</span>
          @endif
          @if ($department['system_item'])
          <span class="system-item">Системная запись!</span>
          @endif
      </a>
    <div class="icon-list">
      <div>
        @can('create', App\Department::class)
        <div class="icon-list-add sprite" data-open="medium-add"></div>
        @endcan
      </div>
      <div>
        @if($department['edit'] == 1)
        <div class="icon-list-edit sprite" data-open="first-edit"></div>
        @endif
      </div>
      <div class="del">
        @if (empty($department['staff']) && empty($department['children']) && ($department['system_item'] != 1) && $department['delete'] == 1)
        <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
        @endif
      </div>
    </div>
    <div class="drop-list checkbox">
      @if ($drop == 1)
      <div class="sprite icon-drop"></div>
      @endif
      <input type="checkbox" name="" class="table-check" id="filial-check-{{ $department['id'] }}"
              {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
              @if(!empty($filter['booklist']['booklists']['default']))
                {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                @if (in_array($department['id'], $filter['booklist']['booklists']['default'])) checked 
              @endif
            @endif
      >
      <label class="label-check white" for="filial-check-{{ $department['id'] }}"></label> 
    </div>
    <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">
      @if (!empty($department['staff']) || !empty($department['children']))
      @if (!empty($department['staff']))
      @foreach($department['staff'] as $staffer)
      {{-- Конечный --}}
      @include('departments.staff-list', $staffer)
      @endforeach
      @endif
      @if (!empty($department['children']))
      @foreach($department['children'] as $department)
      @include('departments.departments-list', $department)
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
@include('includes.scripts.menu-scripts')

@if(!empty($id))
<script type="text/javascript">

  if ('{{ $item }}' == 'department') {

    // Если первый элемент
    if ($('#departments-{{ $id }}').hasClass('first-item')) {
      // Присваиваем активный класс
      $('#departments-{{ $id }}').addClass('first-active');
      // Открываем элемент
      $('#departments-{{ $id }}').children('.medium-list').addClass('is-active');
    };
    // Если средний элемент
    if ($('#departments-{{ $id }}').hasClass('medium-item')) {
      
        // Присваиваем элементу активный клас и открываем его и вышестоящий
        $('#departments-{{ $id }}').addClass('medium-active');
        $('#departments-{{ $id }}').parent('.medium-list').addClass('is-active');
        $('#departments-{{ $id }}').children('.medium-list').addClass('is-active');

        // Перебираем родителей
        $.each($('#departments-{{ $id }}').parents('.item'), function (index) {

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
  };

  if ('{{ $item }}' == 'staffer') {

    if ($('#staff-{{ $id }}').hasClass('medium-as-last')) {
          // Открываем вышестоящий
          $('#staff-{{ $id }}').parent('.medium-list').addClass('is-active');
    };

    // Перебираем родителей
    $.each($('#staff-{{ $id }}').parents('.item'), function (index) {

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