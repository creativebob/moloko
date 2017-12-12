<!--Шаблон для вывода меню с использованием рекурсии-->
@foreach($departments as $department)

@if($department->url() == 'http://crmsystem/1')
  <!--Добавляем класс active для активного пункта меню-->
  <li class="first-item parent" id="departments-{{ $department->id }}" data-name="{{ $department->title }}">
    <!-- метод url() получает ссылку на пункт меню (указана вторым параметром
    при создании объекта LavMenu)-->
    <ul class="icon-list">
      <li><div class="icon-list-add sprite" data-open="department-add"></div></li>
      <li><div class="icon-list-edit sprite" data-open="filial-edit"></div></li>
      <li><div class="icon-list-delete sprite" data-open="item-delete-ajax"></div></li>
    </ul>
    <a data-list="" class="first-link">
      <div class="list-title">
        <div class="icon-open sprite"></div>
        <span class="first-item-name">{{ $department->title }}</span>
        <span class="number"></span>
      </div>
    </a>
@else
  <li class="medium-item parent" id="departments-{{ $department->id }}" data-name="{{ $department->title }}">
    <a class="medium-link">
      <div class="list-title">
        <div class="icon-open sprite"></div>
        <span>{{ $department->title }}</span>
        <span class="number"></span>
      </div>
    </a>
    <ul class="icon-list"><li><div class="icon-list-delete sprite" data-open="item-delete"></div></li>
      <li><div class="icon-list-delete sprite" data-open="item-delete"></div></li>
    </ul>
 @endif
  <!--Формируем дочерние пункты меню
  метод haschildren() проверяет наличие дочерних пунктов меню-->
  @if($department->hasChildren())
    <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
      <!--метод children() возвращает дочерние пункты меню для текущего пункта-->
      @include(env('THEME').'.departments-list', ['departments'=>$department->children()])
    </ul>
  @endif
  </li>
@endforeach

 

         