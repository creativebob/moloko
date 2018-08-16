@php
$drop = 1;
@endphp
{{-- @can('sort', App\Sector::class)
$drop = 1;
@endcan --}}

@if (empty($id))
@php
$id = null;
@endphp
@endif

@foreach ($navigations as $navigation)
{{-- Если Подкатегория --}}
<li class="first-item item @if (isset($navigation->menus)) parent @endif" id="navigations-{{ $navigation->id }}" data-name="{{ $navigation->name }}">
    <a class="first-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $navigation->name }}</span>
        <span class="number">{{ count($navigation->menus) }}</span>
        <span>( {{ $navigation->navigations_category->name }} )</span>

        @if ($navigation->moderation)
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endif

        @if ($navigation->system_item)
        <span class="system-item">Системная запись!</span>
        @endif

    </a>

    <div class="icon-list">

        <div class="controls-list">

            @include ('includes.control.menu-div', ['item' => $navigation, 'class' => $class, 'color' => 'white'])

        </div>

        <div class="actions-list">

            @can('create', $class)
            <div class="icon-list-add sprite" data-open="medium-add"></div>
            @endcan

            @can('update', $navigation)
            @switch($type)

            @case($type == 'modal')
            <div class="icon-list-edit sprite" data-open="first-edit"></div>
            @break

            @case($type == 'edit')
            <a class="icon-list-edit sprite" href="/admin/{{ $entity }}/{{ $navigation['id'] }}/edit"></a>
            @break
            @break

            @endswitch
            @endcan

            <div class="del">
                @can('delete', $navigation)
                @if(($navigation->system_item != 1) && (!isset($navigation->menus)))
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
        <input type="checkbox" name="" id="navigation-check-{{ $navigation->id }}">
        <label class="label-check white" for="navigation-check-{{ $navigation->id }}"></label> 
    </div>
    <ul class="menu vertical medium-list" data-accordion-menu data-multi-open="false">
        @if (count($navigation->menus) > 0)

        

        @include('includes.menu-views.items-list', ['grouped_items' => $navigation->menus->groupBy('parent_id'), 'items' => $navigation->menus->where('parent_id', null), 'entity' => 'menus', 'class' => 'App\Menu'])

        @else
        <li class="empty-item"></li>
        @endif
    </ul>
</li>
@endforeach


{{-- Скрипт чекбоксов и перетаскивания для меню --}}
@include('includes.scripts.sortable-menu-script')

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