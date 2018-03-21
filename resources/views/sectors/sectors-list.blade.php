{{-- Если вложенный --}}
@php
  $count = 0;
@endphp
@if (isset($sector['children']))
  @php
    $count = count($sector['children']);
  @endphp
@endif
<li class="medium-item item @if (isset($sector['children'])) parent @endif" id="sectors-{{ $sector['id'] }}-content" data-name="{{ $sector['sector_name'] }}">
  <a class="medium-link @if($drop == 0) link-small @endif">
    <div class="icon-open sprite"></div>
    <span class="medium-item-name">{{ $sector['sector_name'] }}</span>
    <span class="number">{{ $count }}</span>
    @if ($sector['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($sector['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif
  </a>
  <div class="icon-list">
    <div>
      @can('create', App\Sector::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      @if($sector['edit'] == 1)
      <div class="icon-list-edit sprite" data-open="medium-edit"></div>
      @endif
    </div>
    <div class="del">
      @if (!isset($sector['children']) && ($sector['system_item'] != 1) && $sector['delete'] == 1)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="check-{{ $sector['id'] }}">
    <label class="label-check" for="check-{{ $sector['id'] }}"></label> 
  </div>
  <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
  @if (isset($sector['children']))
    @foreach($sector['children'] as $sector)
      @include('sectors.sectors-list', $sector)
    @endforeach
  @else
    <li class="empty-item"></li>
  @endif
  </ul>
</li>


 
              
    









 

         