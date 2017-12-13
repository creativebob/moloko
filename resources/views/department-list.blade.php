<!--Шаблон для вывода меню с использованием рекурсии-->
@foreach($items as $item)
    <!--Добавляем класс active для активного пункта меню-->
    <li class="first-item parent" id="departments-{{ $item->id }}" data-name="{{ $item->department_name }}">
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
            <span class="first-item-name">{{ $item->department_name }}</span>
            <span class="number"></span>
          </div>
        </a>
        <!--Формируем дочерние пункты меню
        метод haschildren() проверяет наличие дочерних пунктов меню-->
        @if($item->hasChildren())
          <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
            <!--метод children() возвращает дочерние пункты меню для текущего пункта-->
            @include(env('THEME').'.customMenuItems', ['items'=>$item->children()])
            
          </ul>
        @endif
    </li>
@endforeach

 

         