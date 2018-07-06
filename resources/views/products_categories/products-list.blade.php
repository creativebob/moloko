{{-- Если продукт --}}
@php
$drop = 1;
@endphp

<li class="medium-as-last item" id="products-{{ $product['id'] }}" data-name="{{ $product['name'] }}">
  <a class="medium-as-last-link">
    <span>{{ $product['name'] }}</span>
    @if ($product['moderation'] == 1)
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($product['system_item'] == 1)
    <span class="system-item">Системная запись!</span>
    @endif
  </a>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="product-check-{{ $product['id'] }}">
    <label class="label-check" for="product-check-{{ $product['id'] }}"></label> 
  </div>
</li>

