{{-- Шаблон вывода и динамического обновления --}}
@php
  $drop = 1;
@endphp
{{-- @can('sort', App\Region::class)
  $drop = 1;
@endcan --}}


  @foreach ($regions as $region)      
  <li class="first-item item @if ((count($region->areas) > 0) || (count($region->cities) > 0)) parent @endif" id="regions-{{ $region->id }}" data-name="{{ $region->name }}">
    <a class="first-link @if($drop == 0) link-small @endif">
      <div class="icon-open sprite"></div>
      <span class="first-item-name">{{ $region->name }}</span>
      <span class="number">{{ count($region->areas) + count($region->cities) }}</span>
      @if ($region->moderation == 1)
      <span class="no-moderation">Не отмодерированная запись!</span>
      @endif
      @if ($region->system_item == 1)
      <span class="system-item">Системная запись!</span>
      @endif
    </a>
    <div class="drop-list checkbox">
      @if ($drop == 1)
      <div class="sprite icon-drop"></div>
      @endif
      <input type="checkbox" name="" id="region-check-{{ $region->id }}">
      <label class="label-check white" for="region-check-{{ $region->id }}"></label> 
    </div>

    {{-- Список редактирования 
    <ul class="icon-list">
      @can('create', App\City::class)
      <li><div class="icon-list-add sprite" data-open="city-add"></div></li>
      @endcan
      @if (($region->system_item !== 1) && ((count($region->areas) + count($region->cities)) == 0))
        @can('delete', $region)
        <li><div class="icon-list-delete sprite" data-open="item-delete-ajax"></div></li>
        @endcan
      @endif
    </ul> --}}

    <ul class="menu vertical medium-list" data-entity="areas" data-accordion-menu data-multi-open="false">
    @if((count($region->areas) > 0) || (count($region->cities) > 0))
      @foreach ($region->areas as $area)
        <li class="medium-item item @if (count($area->cities) > 0) parent @endif" id="areas-{{ $area->id }}" data-name="{{ $area->name }}">
          <a class="medium-link">
            <div class="icon-open sprite"></div>
            <span>{{ $area->name }}</span>
            <span class="number">{{ count($area->cities) }}</span>
            @if ($area->moderation == 1)
            <span class="no-moderation">Не отмодерированная запись!</span>
            @endif
            @if ($area->system_item == 1)
            <span class="system-item">Системная запись!</span>
            @endif
          </a>
          <div class="drop-list checkbox">
            @if ($drop == 1)
            <div class="sprite icon-drop"></div>
            @endif
            <input type="checkbox" name="" id="area-check-{{ $area->id }}">
            <label class="label-check" for="area-check-{{ $area->id }}"></label> 
          </div>
          <ul class="menu vertical nested last-list" data-entity="cities">
          @if(count($area->cities) > 0)
            @foreach ($area->cities as $city)
              <li class="last-item item" id="cities-{{ $city->id }}" data-name="{{ $city->name }}">
                <a class="last-link">
                  <span>{{ $city->name }}</span>
                  @if ($city->moderation == 1)
                  <span class="no-moderation">Не отмодерированная запись!</span>
                  @endif
                  @if ($city->system_item == 1)
                  <span class="system-item">Системная запись!</span>
                  @endif
                </a>
                <div class="drop-list checkbox">
                  @if ($drop == 1)
                  <div class="sprite icon-drop"></div>
                  @endif
                  <input type="checkbox" name="" id="city-check-{{ $city->id }}">
                  <label class="label-check" for="city-check-{{ $city->id }}"></label> 
                </div>
              </li>
            @endforeach
          @endif
          </ul>
        </li>
      @endforeach
      @if(count($region->cities) > 0)
        @foreach ($region->cities as $city)
          <li class="medium-as-last item" id="cities-{{ $city->id }}" data-name="{{ $city->name }}">
            <a class="medium-as-last-link">
              <span>{{ $city->name }}</span>
              @if ($city->moderation == 1)
              <span class="no-moderation">Не отмодерированная запись!</span>
              @endif
              @if ($city->system_item == 1)
              <span class="system-item">Системная запись!</span>
              @endif
            </a>
            <div class="drop-list checkbox">
              @if ($drop == 1)
              <div class="sprite icon-drop"></div>
              @endif
              <input type="checkbox" name="" id="city-check-{{ $city->id }}">
              <label class="label-check" for="city-check-{{ $city->id }}"></label> 
            </div>
          </li>
        @endforeach
      @endif
    @endif
    </ul>
  </li>
  @endforeach


{{-- Скрипт чекбоксов и перетаскивания для меню --}}
@include('includes.scripts.menu-scripts')

@if(!empty($id))
<script type="text/javascript">

  // Если средний элемент
  if ($('#cities-{{ $id }}').hasClass('medium-item')) {
    // Открываем элемент
    $('#cities-{{ $id }}').parent('.medium-list').addClass('is-active');
    $('#cities-{{ $id }}').closest('.first-item').addClass('first-active');
  };

  // Если город без района
  if ($('#cities-{{ $id }}').hasClass('medium-as-last')) {
    // Открываем элемент
    $('#cities-{{ $id }}').parent('.medium-list').addClass('is-active');
    $('#cities-{{ $id }}').closest('.first-item').addClass('first-active');
  };

  // Если последний элемент
  if ($('#cities-{{ $id }}').hasClass('last-item')) {
    // Присваиваем элементу активный клас и открываем его и вышестоящий
    $('#cities-{{ $id }}').addClass('medium-active');
    $('#cities-{{ $id }}').parent('.last-list').addClass('is-active');

    // Перебираем родителей
    $.each($('#cities-{{ $id }}').parents('.item'), function (index) {

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


