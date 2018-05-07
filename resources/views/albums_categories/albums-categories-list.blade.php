{{-- Если вложенный --}}
@php
  $count = 0;
@endphp
@if (isset($albums_category['children']))
  @php
    $count = count($albums_category['children']);
  @endphp
@endif
<li class="medium-item item @if (isset($albums_category['children'])) parent @endif" id="albums_categories-{{ $albums_category['id'] }}" data-name="{{ $albums_category['name'] }}">
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


 
              
    









 

         