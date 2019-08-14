@php
$drop = 1;
@endphp
{{-- @can('sort', App\Sector::class)
$drop = 1;
@endcan --}}

{{-- Филиалы --}}
@foreach ($departments as $department)

@if($department->parent_id == null)

<li class="first-item item {{ isset($department->childrens) || $department->staff_count > 0 ? 'parent' : '' }}" id="departments-{{ $department->id }}" data-name="{{ $department->name }}">

    <a class="first-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $department->name }}</span>
        <span class="number">{{ (isset($department->childrens) ? $department->childrens->count() : 0) + $department->staff_count }}</span>
        @moderation ($department)
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endmoderation
    </a>

    <div class="icon-list">

        <div class="controls-list">
            <div class="display-menu">
                @can ('display', $department)

                <div class="
                @display ($department)
                icon-display-show
                @else
                        icon-display-hide
                        @enddisplay
                        white sprite" data-open="item-display"></div>

                @endcan
            </div>

            <div class="system-menu">
                {{-- Системный статус --}}
                @php
                $nested = (($department->staff_count > 0) || isset($department->childrens)) ? 1 : 0;
                @endphp
                @can ('system', $department)
                @switch($department)

                @case($department->system == 1 && $department->company_id == null)
                <div class="icon-system-programm white sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @break

                @case($department->system == null && $department->company_id == 1)
                <div class="icon-system-unlock white sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @break

                @case($department->system == 1 && $department->company_id == 1)
                <div class="icon-system-lock white sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @break
                @endswitch
                @endcan

                @if ($department->system == null && $department->company_id == null)
                <div class="icon-system-template white sprite" data-open="item-system" data-nested="{{ $nested }}"></div>
                @endif
            </div>
        </div>

        <div class="actions-list">

            @can('create', $class)
            <div class="icon-list-add sprite" data-open="modal-create"></div>
            @endcan

            @can('update', $department)
            <div class="icon-list-edit sprite sprite-edit" data-open="modal-edit"></div>
            @endcan

            <div class="del">
                @can('delete', $department)
                @if(empty($department->childrens) && ($department->system == null) && ($department->company_id != null) && ($department->staff_count == 0))
                <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
                @endif
                @endcan
            </div>
        </div>

    </div>

    <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" class="table-check" id="filial-check-{{ $department->id }}"
        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter->booklist->booklists->default))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($department->id, $filter->booklist->booklists->default)) checked
        @endif
        @endif
        >
        <label class="label-check white" for="filial-check-{{ $department->id }}"></label>
    </div>

    <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">

        @if (isset($department->childrens) || $department->staff_count > 0)

        {{-- Штат --}}
        @if ($department->staff_count > 0)
        @foreach($department->staff as $staffer)
        @include('departments.staff_list', $staffer)
        @endforeach
        @endif

        {{-- Отделы --}}
        @if (isset($department->childrens))
        @foreach($department->childrens as $department)
        @include('departments.departments_list', $department)
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

@isset($id)
<script type="application/javascript">

  if ('{{ $item }}' == 'departments') {

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

if ('{{ $item }}' == 'staff') {

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
@endisset

@isset ($count)
<script type="application/javascript">
    $('.content-count').text('{{ $count }}');
</script>
@endisset