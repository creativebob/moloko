{{-- Если продукт --}}
@php
$drop = 1;
@endphp

<li class="medium-as-last item" id="services_products-{{ $services_product['id'] }}" data-name="{{ $services_product['name'] }}">
  <a class="medium-as-last-link">
    <span>{{ $services_product['name'] }} (Товар)</span>
    @if ($services_product['moderation'] == 1)
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($services_product['system_item'] == 1)
    <span class="system-item">Системная запись!</span>
    @endif
  </a>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="services_product-check-{{ $services_product['id'] }}">
    <label class="label-check" for="services_product-check-{{ $services_product['id'] }}"></label> 
  </div>
  <div class="icon-list">

    <div class="display-menu">
      @can ('publisher', App\ServicesProduct::class)
      @if ($services_product['display'] == 1)
      <div class="icon-display-show black sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide black sprite" data-open="item-display"></div>
      @endif
      @endcan
    </div>

  </div>
</li>

