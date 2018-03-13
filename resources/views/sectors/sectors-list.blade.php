{{-- Если вложенный --}}
@php
  $count = 0;
@endphp
@if (isset($sector['children']))
  @php
    $count = count($sector['children']);
  @endphp
@endif
<li class="medium-item item @if (isset($sector['children'])) parent @endif" id="sectors-{{ $sector['id'] }}" data-name="{{ $sector['sector_name'] }}">
  <ul class="icon-list">
    <li>
      @can('create', App\Sector::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </li>
    <li>
      @if($sector['edit'] == 1)
      <div class="icon-list-edit sprite" data-open="medium-edit"></div>
      @endif
    </li>
    <li class="del">
      @if (!isset($sector['children']) && ($sector['system_item'] != 1) && $sector['delete'] == 1)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </li>
  </ul>
  <a class="medium-link @if($drop == 0) link-small @endif">
    <div class="list-title">
      <div class="icon-open sprite"></div>
      <span class="medium-item-name">{{ $sector['sector_name'] }}</span>
      <span class="number">{{ $count }}</span>
    </div>
  </a>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" id="check-{{ $sector['id'] }}">
    <label class="label-check" for="check-{{ $sector['id'] }}"></label> 
  </div>
  @if (isset($sector['children']))
    <ul class="menu vertical medium-list accordion-menu sortable" data-accordion-menu data-allow-all-closed data-multi-open="false">
        @foreach($sector['children'] as $sector)
          @include('sectors.sectors-list', $sector)
        @endforeach
    </ul>
  @endif
</li>


 
              
    









 

         