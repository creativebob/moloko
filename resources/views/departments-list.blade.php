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
<li class="medium-item parent" id="departments-{{ $department['id'] }}" data-name="{{ $department['department_name'] }}">
  <a class="medium-link">
    <div class="list-title">
      <div class="icon-open sprite"></div>
      <span>{{ $department['department_name'] }}</span>
      <span class="number">{{ $count }}</span>
    </div>
  </a>
  <ul class="icon-list">
    <li><div class="icon-list-add sprite" data-open="department-add"></div></li>
    <li><div class="icon-list-edit sprite" data-open="department-edit"></div></li>
    <li>
      @if (!isset($department['children']))
        <div class="icon-list-delete sprite" data-open="item-delete"></div>
      @endif
    </li>
  </ul>
  @if (isset($department['staff']) || isset($department['children']))
    <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
      @if (isset($department['children']))
        @foreach($department['children'] as $department)
          @include('departments-list', $department)
        @endforeach
      @endif
      @if (isset($department['staff']))
        @foreach($department['staff'] as $staffer)
          <li class="medium-item parent" id="staff-{{ $staffer['id'] }}" data-name="{{ $staffer['position']['position_name'] }}">
            <div class="medium-as-last">{{ $staffer['position']['position_name'] }} ( <a href="/staff/{{ $staffer['id'] }}/edit" class="link-recursion">
            @if (isset($staffer['user_id']))
              {{ $staffer['user']['first_name'] }} {{ $staffer['user']['second_name'] }}
            @else
              Вакансия
            @endif
            </a> ) 
              <ul class="icon-list">
                <li><div class="icon-list-delete sprite" data-open="item-delete"></div></li>
              </ul>
            </div>
          </li>
        @endforeach
      @endif
    </ul>
  @endif
</li>


 
              
    









 

         