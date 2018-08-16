{{-- Если вложенный --}}
@php
  $count = 0;
@endphp
@if (isset($item->children))
  @php
    $count = count($item->children);
  @endphp
@endif
@foreach ($items as $item)
@if (isset($albums_categories[$item->id]))
<li class="medium-item item parent" id="albums_categories-{{ $item->id }}" data-name="{{ $item->name }}">
  <a class="medium-link @if($drop == 0) link-small @endif">
    <div class="icon-open sprite"></div>
    <span class="medium-item-name">{{ $item->name }}</span>
    <span class="number">{{ $count }}</span>
    @if ($item->moderation)
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($item->system_item)
    <span class="system-item">Системная запись!</span>
    @endif
  </a>
  <div class="icon-list">

    <div class="display-menu">
      @can ('publisher', App\AlbumsCategory::class)
      @if ($item->display == 1)
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
      @if($item->edit == 1)
      <div class="icon-list-edit sprite" data-open="medium-edit"></div>
      @endif
    </div>
    <div class="del">
      @if (!isset($item->children) && ($item->system_item != 1) && $item->delete == 1)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="check-{{ $item->id }}">
    <label class="label-check" for="check-{{ $item->id }}"></label> 
  </div>
  <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
  @if(isset($albums_categories[$item->id]))
        @include('albums_categories.items-list', ['items' => $albums_categories[$item->id]])

    @else
    <li class="empty-item"></li>
  @endif
  </ul>
</li>
@else

{{-- Конечный --}}
<li class="medium-as-last item" id="albums_categories-{{ $item->id }}" data-name="{{ $item->name }}">
  <a class="medium-as-last-link">
    <span>{{ $item->name }}</span>
    @if ($item->moderation)
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($item->system_item)
    <span class="system-item">Системная запись!</span>
    @endif
  </a>
  <div class="icon-list">

    <div class="display-menu">
      @can ('publisher', App\AlbumsCategory::class)
      @if ($item->display == 1)
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
      {{-- @if($item->edit == 1) --}}
      <div class="icon-list-edit sprite" data-open="medium-edit"></div>
      {{-- @endif --}}
    </div>
    <div class="del">
      @if(($item->system_item != 1) && ($item->delete == 1))
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="item-check-{{ $item->id }}">
    <label class="label-check" for="item-check-{{ $item->id }}"></label> 
  </div>
</li>
@endif
@endforeach


 
              
    









 

         