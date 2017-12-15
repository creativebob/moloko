<aside class="sidebar expand" id="sidebar" style="width: 240px;">
  <nav class="nav" id="sidebar-navigation">
    <ul class="vertical menu accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
      <li><a href="filials.php" data-link="1000"><div class="icon-mcc sprite"></div><span>Цуп</span></a></li>
      <li><a href="#" data-link="2000"><div class="icon-sale sprite"></div><span>Продажи</span></a>
        <ul class="menu vertical">
          <li><a href="lead.php" data-link="30000">Лиды</a></li>
          <li><a href="zakaz.php" data-link="40000">Заказы</a></li>
          <li><a href="plan.php" data-link="500000">План продаж</a></li>
        </ul>
      </li>
      <li><a href="#" data-link="6"><div class="icon-finance sprite"></div><span>Финансы</span></a>
        <ul class="menu vertical">
          <li><a href="#" data-link="700000">Расходы</a></li>
          <li><a href="#" data-link="80000">Зарплаты</a></li>
          <li><a href="#" data-link="90000">Финансовый план</a></li>
        </ul>
      </li>
      <li><a href="#" data-link="1000000"><div class="icon-production sprite"></div><span>Производство</span></a>
        <ul class="menu vertical">
          <li><a href="#" data-link="1100">График производства</a></li>
          <li><a href="#" data-link="1200">График монтажа</a></li>
          <li><a href="#" data-link="1300">График доставки</a></li>
          <li><a href="#" data-link="1400">Контроль качетсва</a></li>
          <li><a href="#" data-link="1500">Склад</a></li>
        </ul>
      </li>
      <li><a href="#" data-link="16"><div class="icon-marketing sprite"></div><span>Маркетинг</span></a>
        <ul class="menu vertical">
          <li><a href="#" data-link="1700">Сайт</a></li>
          <li><a href="#" data-link="1800">Акции</a></li>
          <li><a href="#" data-link="1900">Аналитика</a></li>
          <li><a href="#" data-link="2000">Спрос</a></li>
        </ul>
      </li>
      <li><a href="#" data-link="21"><div class="icon-study sprite"></div><span>Обучение</span></a>
        <ul class="menu vertical">
          <li><a href="#" data-link="2200">Скрипты</a></li>
          <li><a href="#" data-link="2300">Тесты</a></li>
        </ul>
      </li>
      <li><a href="#" data-link="24"><div class="icon-guide sprite"></div><span>Справочники</span></a>
        <ul class="menu vertical">
          <li><a href="#" data-link="2900">Филиалы</a>
            <ul class="menu vertical nested">
              <li><a href="#" data-link="3000">Отделы</a></li>
              <li><a href="#" data-link="3100">Сотрудники</a></li>
            </ul>
          </li>
          <li><a href="#" data-link="3200">Города</a></li>
          <li><a href="#" data-link="3300">Должности</a></li>
        </ul>
      </li>
      <li><a href="#" data-link="34"><div class="icon-chatbot sprite"></div><span>Чат-бот</span></a>
        <ul class="menu vertical">
          <li><a href="#" data-link="3500">Разработка</a></li>
          <li><a href="#" data-link="3600">Отчеты</a></li>
        </ul>
      </li>
      <li><a href="#" data-link="37"><div class="icon-settings sprite"></div><span>Настройки</span></a>
        <ul class="menu vertical">
          @foreach ($menu as $item)
          <li><a href="{{ $item->page_alias }}" data-link="{{ $item->id }}">{{ $item->page_name }}</a></li>
          @endforeach
        </ul>
      </li>
    </ul>
  </nav>
  <section class="menu vertical gen-menu-bot">
    <div id="sidebar-button">
      <div class="icon-arrow-back sprite" id="cursor"></div>
    </div> 
  </section>
</aside>