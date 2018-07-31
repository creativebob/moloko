{{-- Если вложенный --}}
@php
$count = 0;
@endphp
@if (isset($goods_category['children']))
@php
$count = count($goods_category['children']) + $count;
@endphp
@endif

@if (isset($goods_category['children']))
<li class="medium-item item parent" id="goods_categories-{{ $goods_category['id'] }}" data-name="{{ $goods_category['name'] }}">
    <a class="medium-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="medium-item-name">{{ $goods_category['name'] }}</span>
        <span class="number">{{ $count }}</span>
        @if ($goods_category['moderation'])
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endif
        @if ($goods_category['system_item'])
        <span class="system-item">Системная запись!</span>
        @endif

        {{-- @can ('publisher', App\GoodsCategory::class)
        @if ($goods_category['display'] == 1)
        <span class="system-item">Отображается на сайте</span>
        @else
        <span class="no-moderation">Не отображается на сайте</span>
        @endif
        @endcan --}}

    </a>
    <div class="icon-list">

        <div class="display-menu">
            @can ('publisher', App\GoodsCategory::class)
            @if ($goods_category['display'] == 1)
            <div class="icon-display-show black sprite" data-open="item-display"></div>
            @else
            <div class="icon-display-hide black sprite" data-open="item-display"></div>
            @endif
            @endcan
        </div>

        <div>
            @can('create', App\GoodsCategory::class)
            <div class="icon-list-add sprite" data-open="medium-add"></div>
            @endcan
        </div>
        <div>
            @if($goods_category['edit'] == 1)
            <a class="icon-list-edit sprite" href="/admin/goods_categories/{{ $goods_category['id'] }}/edit"></a>
            @endif
        </div>
        <div class="del">
            @if (!isset($goods_category['children']) && empty($goods_category['goods_products']) && ($goods_category['system_item'] != 1) && $goods_category['delete'] == 1 && $goods_category['goods_products_count'] > 0)
            <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
            @endif
        </div>
    </div>
    <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" id="check-{{ $goods_category['id'] }}">
        <label class="label-check" for="check-{{ $goods_category['id'] }}"></label> 
    </div>
    <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
        @if ((isset($goods_category['children'])) || ($goods_category['goods_products_count'] > 0))

        @if (isset($goods_category['children']))
        @foreach($goods_category['children'] as $goods_category)
        @include('goods_categories.goods-categories-list', $goods_category)
        @endforeach
        @endif

        @else
        <li class="empty-item"></li>
        @endif
    </ul>
</li>

@else

{{-- Конечный --}}
<li class="medium-as-last item" id="goods_categories-{{ $goods_category['id'] }}" data-name="{{ $goods_category['name'] }}">
  <a class="medium-as-last-link">
    <span>{{ $goods_category['name'] }}</span>
    @if ($goods_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($goods_category['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif

</a>
<div class="icon-list">

    <div class="display-menu">
      @can ('publisher', App\GoodsCategory::class)
      @if ($goods_category['display'] == 1)
      <div class="icon-display-show black sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide black sprite" data-open="item-display"></div>
      @endif
      @endcan
  </div>

  <div>
      @can('create', App\GoodsCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
  </div>
  <div>
      {{-- @if($goods_category['edit'] == 1) --}}
      <a class="icon-list-edit sprite" href="/admin/goods_categories/{{ $goods_category['id'] }}/edit"></a>
      {{-- @endif --}}
  </div>
  <div class="del">
      @if(($goods_category['system_item'] != 1) && ($goods_category['delete'] == 1))
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
  </div>
</div>
<div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="goods_category-check-{{ $goods_category['id'] }}">
    <label class="label-check" for="goods_category-check-{{ $goods_category['id'] }}"></label> 
</div>
</li>
@endif
















