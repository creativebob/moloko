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
	      <h2 class="header-content">Список компани</h2>
	      <a href="/companies/create" class="icon-add sprite"></a>
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

          {{ Form::open(['route' => 'companies.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}

          <legend>Фильтрация</legend>
            <div class="grid-x grid-padding-x"> 
              <div class="small-6 cell">
                <label>Статус пользователя
                  {{ Form::select('contragent_status', [ 'all' => 'Все пользователи','1' => 'Сотрудник', '2' => 'Клиент'], 'all') }}
                </label>
              </div>
              <div class="small-6 cell">
                <label>Блокировка доступа
                  {{ Form::select('access_block', [ 'all' => 'Все пользователи', '1' => 'Доступ блокирован', '' => 'Доступ открыт'], 'all') }}
                </label>
              </div>
              <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
               {{ Form::submit('Фильтрация', ['class'=>'button']) }}
              </div>
            </div>

          {{ Form::close() }}

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
          <th class="td-company-name">Название компании</th>
          <th class="td-company-address">Адрес</th>
          <th class="td-company-phone">Телефон</th>
          <th class="td-user_id">Руководитель</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($companies))
        @foreach($companies as $company)
        <tr id="{{ $company->id }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $company->id }}"><label class="label-check" for="check-{{ $company->id }}"></label></td>
          <td class="td-company-name">{{ link_to_route('companies.edit', $company->company_name, [$company->id]) }} </td>
          <td class="td-company-address">{{ $company->company_address }} </td>
          <td class="td-company-phone">{{ decorPhone($company->company_phone) }} </td>
          <td class="td-user_id">{{ $company->user_info->first_name or ' ... ' }} {{ $company->user_info->second_name or ' ... ' }} </td>

          <td class="td-delete"><a class="icon-delete sprite"></a></td>       
        </tr>
        @endforeach
      @endif
      </tbody>
    </table>
  </div>
</div>

{{-- Pagination --}}
<div class="grid-x" id="pagination">
  <div class="small-6 cell pagination-head">
    <span class="pagination-title">Кол-во записей: {{ $companies->count() }}</span>
    {{ $companies->links() }}
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


$(document).on('click', '.td-delete', function() {
      var id = $(this).parent('tr').attr('id');
      // Ajax
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/companies/' + id,
        type: "DELETE",
        data: {'id': id},

        success: function (data) {
          var result = $.parseJSON(data);
          if (result.status == 1) {
            $('.tbody-width>tr#' + id).remove();
          } else {
            alert(reuslt.msg);
          };
        }
      });
  });

});


$(window).scroll(function () {
  if ($('#thead-sticky').hasClass('is-stuck')) {
    fixedThead ();
  };
});


    </script>
@endsection