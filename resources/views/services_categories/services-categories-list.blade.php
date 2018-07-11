{{-- Если вложенный --}}
@php
$count = 0;
@endphp
@if (isset($services_category['children']))
@php
$count = count($services_category['children']) + $count;
@endphp
@endif
@if (isset($services_category['services_products']))
@php
$count = count($services_category['services_products']) + $count;
@endphp
@endif
@if ((isset($services_category['children'])) || ($services_category['services_products_count'] > 0))
<li class="medium-item item parent" id="services_categories-{{ $services_category['id'] }}" data-name="{{ $services_category['name'] }}">
  <a class="medium-link @if($drop == 0) link-small @endif">
    <div class="icon-open sprite"></div>
    <span class="medium-item-name">{{ $services_category['name'] }}</span>
    <span class="number">{{ $count }}</span>
    @if ($services_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($services_category['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif

    {{-- @can ('publisher', App\ServicesCategory::class)
    @if ($services_category['display'] == 1)
    <span class="system-item">Отображается на сайте</span>
    @else
    <span class="no-moderation">Не отображается на сайте</span>
    @endif
    @endcan --}}

  </a>
  <div class="icon-list">

    <div class="display-menu">
      @can ('publisher', App\ServicesCategory::class)
      @if ($services_category['display'] == 1)
      <div class="icon-display-show black sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide black sprite" data-open="item-display"></div>
      @endif
      @endcan
    </div>

    <div>
      @can('create', App\ServicesCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      @if($services_category['edit'] == 1)
      <a class="icon-list-edit sprite" href="/services_categories/{{ $services_category['id'] }}/edit"></a>
      @endif
    </div>
    <div class="del">
      @if (!isset($services_category['children']) && empty($services_category['services_products']) && ($services_category['system_item'] != 1) && $services_category['delete'] == 1 && $services_category['services_products_count'] > 0)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="check-{{ $services_category['id'] }}">
    <label class="label-check" for="check-{{ $services_category['id'] }}"></label> 
  </div>
  <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
    @if ((isset($services_category['children'])) || ($services_category['services_products_count'] > 0))

    @if ($services_category['services_products_count'] > 0)
    @foreach($services_category['services_products'] as $product)
    @include('services_categories.services-products-list', $product)
    @endforeach
    @endif

    @if (isset($services_category['children']))
    @foreach($services_category['children'] as $services_category)
    @include('services_categories.services-categories-list', $services_category)
    @endforeach
    @endif

    @else
    <li class="empty-item"></li>
    @endif
  </ul>
</li>

@else

{{-- Конечный --}}
<li class="medium-as-last item" id="services_categories-{{ $services_category['id'] }}" data-name="{{ $services_category['name'] }}">
  <a class="medium-as-last-link">
    <span>{{ $services_category['name'] }}</span>
    @if ($services_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($services_category['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif

  </a>
  <div class="icon-list">

    <div class="display-menu">
      @can ('publisher', App\ServicesCategory::class)
      @if ($services_category['display'] == 1)
      <div class="icon-display-show black sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide black sprite" data-open="item-display"></div>
      @endif
      @endcan
    </div>

    <div>
      @can('create', App\ServicesCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      {{-- @if($services_category['edit'] == 1) --}}
      <a class="icon-list-edit sprite" href="/services_categories/{{ $services_category['id'] }}/edit"></a>
      {{-- @endif --}}
    </div>
    <div class="del">
      @if(($services_category['system_item'] != 1) && ($services_category['delete'] == 1))
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="services_category-check-{{ $services_category['id'] }}">
    <label class="label-check" for="services_category-check-{{ $services_category['id'] }}"></label> 
  </div>
</li>
@endif
















