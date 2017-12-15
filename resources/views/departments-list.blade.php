<!--  -->
@if (isset($department['children']))
  @php
    $count = 0;
  @endphp
  @foreach ($employees as $employee)
    @if($department['id'] == $employee->department_id)
      @php
        $count = $count + 1;
      @endphp
    @endif
  @endforeach
  @php
    $count = count($department['children']) + $count;
  @endphp
@else
  @php
    $count = 0;
  @endphp
  @foreach ($employees as $employee)
    @if($department['id'] == $employee->department_id)
      @php
        $count = $count + 1;
      @endphp
    @endif
  @endforeach
@endif


@if($department['filial_status'] == 1)
  {{-- Если родитель --}}
  
  <li class="first-item parent" id="departments-{{ $department['id'] }}" data-name="{{ $department['department_name'] }}">
    <ul class="icon-list">
      <li><div class="icon-list-add sprite" data-open="department-add"></div></li>
      <li><div class="icon-list-edit sprite" data-open="filial-edit"></div></li>
      <li>
        @if (!isset($department['children']))
          <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
        @endif
      </li>
    </ul>
    <a data-list="" class="first-link">
      <div class="list-title">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $department['department_name'] }}</span>
        <span class="number">{{ $count }}</span>
      </div>
    </a>
@else
  {{-- Если вложенный --}}
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
@endif

  <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
  {{-- список должностей, не относящимся к отделам или во вложенности --}}
  @foreach ($employees as $employee)
    @if($department['id'] == $employee->department_id)
      @foreach ($positions as $position)
        @if($position->id == $employee->position_id)
          <li class="medium-item parent" id="employees-{{ $employee->id }}" data-name="{{ $position->position_name }}">
            <div class="medium-as-last">{{ $position->position_name }} (
            @if (isset($employee->user_id))
            @else
              <a class="link-recursion">Вакансия</a>
            @endif
            )
              <ul class="icon-list">
                <li><div class="icon-list-delete sprite" data-open="item-delete"></div></li>
              </ul>
            </div>
        @endif
      @endforeach
    @endif
  @endforeach

  @if (isset($department['children']))
    @foreach($department['children'] as $department)
      @include('departments-list', $department)
    @endforeach
  @else
    <ul class="menu vertical nested last-list">
      @foreach ($employees as $employee)
        @if($department['id'] == $employee->department_id)
          @foreach ($positions as $position)
            @if($position->id == $employee->position_id)
              <li class="last-item parent" id="employees-{{ $employee->id }}" data-name="{{ $position->position_name }}">
                <div class="last-link">{{ $position->position_name }} (
                  @if (isset($employee->user_id))

                  @else
                    Вакансия
                  @endif
                  )
                  <ul class="icon-list">
                    <li><div class="icon-list-delete sprite" data-open="item-delete"></div></li>
                  </ul>
                </div>
              </li>
            @endif
          @endforeach
        @endif
      @endforeach
    </ul>
  @endif
  </ul>
</li>








 

         