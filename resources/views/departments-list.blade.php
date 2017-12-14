
@if($department['filial_status'] == 1)
  <!-- Если родитель -->
  <li class="first-item parent" id="departments-{{ $department['id'] }}" data-name="{{ $department['department_name'] }}">
    <ul class="icon-list">
      <li><div class="icon-list-add sprite" data-open="department-add"></div></li>
      <li><div class="icon-list-edit sprite" data-open="filial-edit"></div></li>
      <li>
        @if (isset($department['children']))
          
        @else
          <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
        @endif
      </li>
    </ul>
    <a data-list="" class="first-link">
      <div class="list-title">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $department['department_name'] }}</span>
        <span class="number">
        @if (isset($department['children']))
          {{ count($department['children']) }}
        @else
          0
        @endif
        </span>
      </div>
    </a>
@else
  <!-- Если вложенный -->
  <li class="medium-item parent" id="departments-{{ $department['id'] }}" data-name="{{ $department['department_name'] }}">
    <a class="medium-link">
      <div class="list-title">
        <div class="icon-open sprite"></div>
        <span>{{ $department['department_name'] }}</span>
        <span class="number">
        @if (isset($department['children']))
          {{ count($department['children']) }}
        @else
          0
        @endif</span>
      </div>
    </a>
    <ul class="icon-list">
      <li><div class="icon-list-add sprite" data-open="department-add"></div></li>
      <li><div class="icon-list-edit sprite" data-open="department-edit"></div></li>
      <li>
        @if (isset($department['children']))
          
        @else
          <div class="icon-list-delete sprite" data-open="item-delete"></div>
        @endif</li>
    </ul>
 @endif

 @if (isset($department['children']))
    <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
    @foreach($department['children'] as $department)
      @include('departments-list', $department)
    @endforeach
    </ul>
  @endif

</li>




 

         