{{-- Если вложенный --}}
@php
  $count = 0;
@endphp
@if (isset($albums_category['children']))
  @php
    $count = count($albums_category['children']);
  @endphp
@endif
@if (isset($albums_category['children']))
<li class="medium-item item parent" id="albums_categories-{{ $albums_category['id'] }}" data-name="{{ $albums_category['name'] }}">
  <a class="medium-link @if($drop == 0) link-small @endif">
    <div class="icon-open sprite"></div>
    <span class="medium-item-name">{{ $albums_category['name'] }}</span>
    <span class="number">{{ $count }}</span>
    @if ($albums_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($albums_category['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif
  </a>
  <div class="icon-list">

    <div class="display-menu">
      @can ('publisher', App\AlbumsCategory::class)
      @if ($albums_category['display'] == 1)
      <div class="icon-display-show black sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide black sprite" data-open="item-display"></div>
      @endif
      @endcan
    </div>
    
    <div>
      @can('create', App\AlbumsCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      @if($albums_category['edit'] == 1)
      <div class="icon-list-edit sprite" data-open="medium-edit"></div>
      @endif
    </div>
    <div class="del">
      @if (!isset($albums_category['children']) && ($albums_category['system_item'] != 1) && $albums_category['delete'] == 1)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="check-{{ $albums_category['id'] }}">
    <label class="label-check" for="check-{{ $albums_category['id'] }}"></label> 
  </div>
  <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
  @if (isset($albums_category['children']))
    @foreach($albums_category['children'] as $albums_category)
      @include('albums_categories.albums-categories-list', $albums_category)
    @endforeach
  @else
    <li class="empty-item"></li>
  @endif
  </ul>
</li>
@else

{{-- Конечный --}}
<li class="medium-as-last item" id="albums_categories-{{ $albums_category['id'] }}" data-name="{{ $albums_category['name'] }}">
  <a class="medium-as-last-link">
    <span>{{ $albums_category['name'] }}</span>
    @if ($albums_category['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($albums_category['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif
  </a>
  <div class="icon-list">

    <div class="display-menu">
      @can ('publisher', App\AlbumsCategory::class)
      @if ($albums_category['display'] == 1)
      <div class="icon-display-show black sprite" data-open="item-display"></div>
      @else
      <div class="icon-display-hide black sprite" data-open="item-display"></div>
      @endif
      @endcan
    </div>

    <div>
      @can('create', App\AlbumsCategory::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      {{-- @if($albums_category['edit'] == 1) --}}
      <div class="icon-list-edit sprite" data-open="medium-edit"></div>
      {{-- @endif --}}
    </div>
    <div class="del">
      @if(($albums_category['system_item'] != 1) && ($albums_category['delete'] == 1))
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="albums_category-check-{{ $albums_category['id'] }}">
    <label class="label-check" for="albums_category-check-{{ $albums_category['id'] }}"></label> 
  </div>
</li>
@endif


 
              
    









 

         