@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.table-inhead')
@endsection

@section('title', 'Пользователи')

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
	  <div class="top-bar head-content">
	    <div class="top-bar-left">
	      <h2 class="header-content">Пользователи системы</h2>
	      <a href="/user" class="icon-add sprite"></a>
	    </div>
	    <div class="top-bar-right">
	      <a class="icon-filter sprite"></a>
	      <input class="search-field" type="search" name="search_field" placeholder="Поиск" />
	      <button type="button" class="icon-search sprite button"></button>
	    </div>
	  </div>
	  {{-- Блок фильтров --}}
	  <div class="grid-x">
      <div class="small-12 cell filters" id="filters">
        <fieldset class="fieldset-filters">
          <legend>Фильтрация</legend>
          <div>lol</div>
          <div>lol</div>
          <div>lol</div>
          <div>lol</div>
        </fieldset>
      </div>
    </div>
	</div>
</div>
@endsection
 
@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="table-content" data-sticky-container>
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-surname">Фамилия</th>
          <th class="td-name">Имя</th>
          <th class="td-phone">Телефон</th>
          <th class="td-status">Статус</th>
          <th class="td-login">Логин</th>
          <th class="td-access">Доступ</th>
          <th class="td-access-level">Уровень доступа</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">

        @foreach($users as $user)
        <tr>
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check1"><label class="label-check" for="check1"></label></td>
          <td class="td-surname">{{ $user->second_name }}</td>
          <td class="td-name">{{ $user->first_name }}</td>
          <td class="td-phone">{{ $user->phone }}</td>
          <td class="td-status">{{ $user->contragen_status }}</td>
          <td class="td-login">{{ $user->login }}</td>
          <td class="td-access">{{ $user->access_block }}</td>
          <td class="td-access-level">Уровень доступа</td>
          <td class="td-delete"><a class="icon-delete sprite"></a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
{{-- Pagination --}}
<div class="grid-x" id="pagination">
  <div class="small-6 cell pagination-head">
    <span class="pagination-title">Кол-во записей: 3</span>
    <ul class="pagination" role="navigation" aria-label="Pagination">
      <li class="current">1</li>
      <li><a href="#" aria-label="Page 2">2</a></li>
      <li><a href="#" aria-label="Page 3">3</a></li>
      <li><a href="#" aria-label="Page 4">4</a></li>
      <li class="ellipsis" aria-hidden="true"></li>
      <li><a href="#" aria-label="Page 12">12</a></li>
      <li><a href="#" aria-label="Page 13">13</a></li>
    </ul>
  </div>
  <div class="small-6 cell">
    <div class="right">
      <a href="#" data-open="modal"><div class="sprite icon-deleted"></div>6</a>
    </div>
    <div class="reveal" id="modal" data-reveal>
      <div class="grid-x">
        <div class="small-12 cell modal-title">
          <h5>ДОБАВЛЕНИЕ НАСЕЛЕННОГО ПУНКТА</h5>
        </div>
      </div>
      <div class="grid-x grid-padding-x modal-content inputs">
        <div class="small-10 medium-4 cell">
          <label>Область
            <input type="text" name="" required>
            <span class="form-error">Уж постарайтесь, придумайте что-нибудь!</span>
          </label>
          <label>Район
            <input type="text" name="" required>
            <span class="form-error">Уж постарайтесь, придумайте что-нибудь!</span>
          </label>
        </div>
        <div class="small-12 medium-8 cell">
          <div class="grid-x grid-padding-x">
            <div class="small-10 medium-8 cell">
              <label>Название населенного пункта
                <input type="text" name="" required>
                <span class="form-error">Уж постарайтесь, придумайте что-нибудь!</span>
              </label>
            </div>
          </div>
          <table class="table-content-search">
            <caption>Результаты поиска в сторонней базе данных:</caption>
            <tbody>
              <tr>
                <td><a href="#">Кимильтей</a></td>
                <td><a href="#">Куйтунский район</a></td>
                <td><a href="#">Иркутская область</a></td>
              </tr>
              <tr>
                <td><a href="#">Кимильтей</a></td>
                <td><a href="#">Куйтунский район</a></td>
                <td><a href="#">Иркутская область</a></td>
              </tr>
              <tr>
                <td><a href="#">Кимильтей</a></td>
                <td><a href="#">Куйтунский район</a></td>
                <td><a href="#">Иркутская область</a></td>
              </tr>
            </tbody>
          </table>
          <div class="grid-x ">
            <div class="small-6 small-centered cell">
              <a href="#" class="button modal-button">Сохранить</a>
            </div>
          </div>
        </div>
      </div>
      <div data-close class="icon-close-modal sprite close-modal"></div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

$(document).ready(function () {  
  $("#table-content").tablesorter({ 
  // передаем аргументы для заголовков и назначаем объект 
    headers: { 
      // работаем со второй колонкой (подсчет идет с нуля) 
      0: { 
      // запрет сортировки указанием свойства 
      sorter: false 
      }, 
    // работаем со третьей колонкой (подсчет идет с нуля) 
      1: { 
    // запрещаем, использовав свойство 
      sorter: false 
      },
    },
    sortList: [[2,0]],
    cssHeader: "thead-header"
  });  
}); 

$(function() {
  // Сортировка строк таблицы
  // Оставляем ширину у вырванного из потока элемента
  var fixHelper = function(e, ui) {
    ui.children().each(function() {
      $(this).width($(this).width());
    });
    return ui;
  };
  // Включаем перетаскивание
  $("#table-content tbody").sortable({
      helper: fixHelper, // ширина вырванного элемента
      handle: 'td:first' // указываем за какой элемент можно тянуть
  }).disableSelection();

  // Чекбоксы
  console.log('Запуск функции чекбоксов');
  var checkboxes = document.querySelectorAll('input.table-check');
  var checkall = document.getElementById('check-all');
  console.log('Видим общее количество чекбоксов = ' + checkboxes.length);

  for(var i=0; i<checkboxes.length; i++) {
    checkboxes[i].onclick = function() {
      var checkedCount = document.querySelectorAll('input.table-check:checked').length;
      console.log('Берем выделенные чекбоксы  = ' + checkedCount);
      checkall.checked = checkedCount > 0;
      checkall.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
      console.log('Ставим главному статус ' + checkall.checked + ' и меняем спрайт');
    };
  };
  checkall.onclick = function() {
    for(var i=0; i<checkboxes.length; i++) {
      checkboxes[i].checked = this.checked;
      console.log('Видим клик по главному, ставим его положение всем = ' + this.checked);
    };
  };
  console.log('Завершение функции чекбоксов');
  console.log('-----');
});


$(window).scroll(function () {
  if ($('#thead-sticky').hasClass('is-stuck')) {
    fixedThead ();
  };
});

    </script>
@endsection