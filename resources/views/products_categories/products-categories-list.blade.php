{{-- Если вложенный --}}
@php
  $count = 0;
@endphp
@if (isset($products_category['children']))
  @php
    $count = count($products_category['children']);
  @endphp
@endif
@if (isset($products_category['children']))
<li class="medium-item item parent" id="products_categories-{{ $products_category['id'] }}" data-name="{{ $products_category['name'] }}">
  <a class="medium-link @if($drop == 0) link-small @endif">
    <div class="icon-open sprite"></div>
    <span class="medium-item-name">{{ $products_category['name'] }}</span>
    <span class="number">{{ $count }}</span>
    @if ($products_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($products_category['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif
    @can ('publisher', App\ProductsCategory::class)
    @if ($products_category['display'] == 1)
    <span class="system-item">Отображается на сайте</span>
    @else
    <span class="no-moderation">Не отображается на сайте</span>
    @endif
    @endcan
  </a>
  <div class="icon-list">
    <div>
      @can('create', App\ProductsCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      @if($products_category['edit'] == 1)
      <a class="icon-list-edit sprite" href="/products_categories/{{ $products_category['id'] }}/edit"></a>
      @endif
    </div>
    <div class="del">
      @if (!isset($products_category['children']) && ($products_category['system_item'] != 1) && $products_category['delete'] == 1)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="check-{{ $products_category['id'] }}">
    <label class="label-check" for="check-{{ $products_category['id'] }}"></label> 
  </div>
  <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
  @if (isset($products_category['children']))
    @foreach($products_category['children'] as $products_category)
      @include('products_categories.products-categories-list', $products_category)
    @endforeach
  @else
    <li class="empty-item"></li>
  @endif
  </ul>
</li>

@else

{{-- Конечный --}}
<li class="medium-as-last item" id="products_categories-{{ $products_category['id'] }}" data-name="{{ $products_category['name'] }}">
  <a class="medium-as-last-link">
    <span>{{ $products_category['name'] }}</span>
    @if ($products_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($products_category['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif
    @can ('publisher', App\ProductsCategory::class)
    @if ($products_category['display'] == 1)
    <span class="system-item">Отображается на сайте</span>
    @else
    <span class="no-moderation">Не отображается на сайте</span>
    @endif
    @endcan
  </a>
  <div class="icon-list">
    <div>
      @can('create', App\ProductsCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      {{-- @if($products_category['edit'] == 1) --}}
      <a class="icon-list-edit sprite" href="/products_categories/{{ $products_category['id'] }}/edit"></a>
      {{-- @endif --}}
    </div>
    <div class="del">
      @if(($products_category['system_item'] != 1) && ($products_category['delete'] == 1))
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="products_category-check-{{ $products_category['id'] }}">
    <label class="label-check" for="products_category-check-{{ $products_category['id'] }}"></label> 
  </div>
</li>
@endif


 
              
    









 

         