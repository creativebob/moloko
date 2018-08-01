{{-- Если вложенный --}}
@php
$count = 0;
@endphp
@if (isset($raws_category['children']))
@php
$count = count($raws_category['children']) + $count;
@endphp
@endif

@if (isset($raws_category['children']))
<li class="medium-item item parent" id="raws_categories-{{ $raws_category['id'] }}" data-name="{{ $raws_category['name'] }}">
    <a class="medium-link @if($drop == 0) link-small @endif">
        <div class="icon-open sprite"></div>
        <span class="medium-item-name">{{ $raws_category['name'] }}</span>
        <span class="number">{{ $count }}</span>
        @if ($raws_category['moderation'])
        <span class="no-moderation">Не отмодерированная запись!</span>
        @endif
        @if ($raws_category['system_item'])
        <span class="system-item">Системная запись!</span>
        @endif

        {{-- @can ('publisher', App\RawsCategory::class)
        @if ($raws_category['display'] == 1)
        <span class="system-item">Отображается на сайте</span>
        @else
        <span class="no-moderation">Не отображается на сайте</span>
        @endif
        @endcan --}}

    </a>
    <div class="icon-list">

        <div class="display-menu">
            @can ('publisher', App\RawsCategory::class)
            @if ($raws_category['display'] == 1)
            <div class="icon-display-show black sprite" data-open="item-display"></div>
            @else
            <div class="icon-display-hide black sprite" data-open="item-display"></div>
            @endif
            @endcan
        </div>

        <div>
            @can('create', App\RawsCategory::class)
            <div class="icon-list-add sprite" data-open="medium-add"></div>
            @endcan
        </div>
        <div>
            @if($raws_category['edit'] == 1)
            <a class="icon-list-edit sprite" href="/admin/raws_categories/{{ $raws_category['id'] }}/edit"></a>
            @endif
        </div>
        <div class="del">
            @if (!isset($raws_category['children']) && empty($raws_category['raws_products']) && ($raws_category['system_item'] != 1) && $raws_category['delete'] == 1 && $raws_category['raws_products_count'] > 0)
            <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
            @endif
        </div>
    </div>
    <div class="drop-list checkbox">
        @if ($drop == 1)
        <div class="sprite icon-drop"></div>
        @endif
        <input type="checkbox" name="" id="check-{{ $raws_category['id'] }}">
        <label class="label-check" for="check-{{ $raws_category['id'] }}"></label> 
    </div>
    <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
        @if ((isset($raws_category['children'])) || ($raws_category['raws_products_count'] > 0))

        @if (isset($raws_category['children']))
        @foreach($raws_category['children'] as $raws_category)
        @include('raws_categories.raws-categories-list', $raws_category)
        @endforeach
        @endif

        @else
        <li class="empty-item"></li>
        @endif
    </ul>
</li>

@else

{{-- Конечный --}}
<li class="medium-as-last item" id="raws_categories-{{ $raws_category['id'] }}" data-name="{{ $raws_category['name'] }}">
  <a class="medium-as-last-link">
    <span>{{ $raws_category['name'] }}</span>
    @if ($raws_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($raws_category['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif

</a>
<div class="icon-list">

    <div class="display-menu">
      @can ('publisher', App\RawsCategory::class)
      @if ($raws_category['display'] == 1)
      <div class="icon-display-show black sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide black sprite" data-open="item-display"></div>
      @endif
      @endcan
  </div>

  <div>
      @can('create', App\RawsCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
  </div>
  <div>
      {{-- @if($raws_category['edit'] == 1) --}}
      <a class="icon-list-edit sprite" href="/admin/raws_categories/{{ $raws_category['id'] }}/edit"></a>
      {{-- @endif --}}
  </div>
  <div class="del">
      @if(($raws_category['system_item'] != 1) && ($raws_category['delete'] == 1))
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
  </div>
</div>
<div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="raws_category-check-{{ $raws_category['id'] }}">
    <label class="label-check" for="raws_category-check-{{ $raws_category['id'] }}"></label> 
</div>
</li>
@endif
















