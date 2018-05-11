{{-- Если вложенный --}}
@php
$count = 0;
@endphp
@if (isset($department['children']))
@php
$count = count($department['children']) + $count;
@endphp
@endif
@if (isset($department['staff']))
@php
$count = count($department['staff']) + $count;
@endphp
@endif
<li class="medium-item item @if (isset($department['children']) || isset($department['staff'])) parent @endif" id="departments-{{ $department['id'] }}" data-name="{{ $department['department_name'] }}">
  <a class="medium-link">
    <div class="icon-open sprite"></div>
    <span>{{ $department['department_name'] }}</span>
    <span class="number">{{ $count }}</span>
    @if ($department['moderation'])
    <span class="no-moderation">Не отмодерированная запись!</span>
    @endif
    @if ($department['system_item'])
    <span class="system-item">Системная запись!</span>
    @endif
  </a>
  <div class="drop-list checkbox">
    @if ($drop == 1)
    <div class="sprite icon-drop"></div>
    @endif
    <input type="checkbox" name="" class="table-check" id="department-check-{{ $department['id'] }}"
        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
          {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
          @if (in_array($department['id'], $filter['booklist']['booklists']['default'])) checked 
        @endif
      @endif
    >
    <label class="label-check" for="department-check-{{ $department['id'] }}"></label> 
  </div>
  <div class="icon-list">
    <div>
      @can('create', App\Department::class)
      <div class="icon-list-add sprite" data-open="medium-add"></div>
      @endcan
    </div>
    <div>
      {{-- @if($department['edit'] == 1) --}}
      <div class="icon-list-edit sprite" data-open="medium-edit"></div>
      {{-- @endif --}}
    </div>
    <div class="del">
      @if (empty($department['staff']) && empty($department['children']) && ($department['system_item'] != 1) && $department['delete'] == 1)
      <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
      @endif
    </div>
  </div>
  <ul class="menu vertical medium-list nested" data-accordion-menu data-multi-open="false">
    @if (!empty($department['staff']) || !empty($department['children']))
    @if (!empty($department['staff']))
    @foreach($department['staff'] as $staffer)
    {{-- Конечный --}}
    @include('departments.staff-list', $staffer)
    @endforeach
    @endif
    @if (!empty($department['children']))
    @foreach($department['children'] as $department)
    @include('departments.departments-list', $department)
    @endforeach
    @endif
    @else
    <li class="empty-item"></li>
    @endif
  </ul>
</li>
















