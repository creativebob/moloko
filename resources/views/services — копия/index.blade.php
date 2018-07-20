@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('exel')
<!-- <a href="{{ URL::to('productsDownload/xls') }}"><button class="button">Скачать Excel xls</button></a> -->
<a href="{{ URL::to('/products_download/xlsx') }}">
  <img src="/img/svg/excel_export.svg">
  <!--  <button class="button">Скачать Excel xlsx</button> -->
</a>
<a>
  <img src="/img/svg/excel_import.svg" data-toggle="exel-import">
</a>
<!-- <button class="button" type="button" data-toggle="exel-import">Загрузить</button> -->
<div class="dropdown-pane" id="exel-import" data-dropdown data-auto-focus="true" data-close-on-click="true">
  {{ Form::open(['url' => '/products_import', 'data-abide', 'novalidate', 'files'=>'true']) }}
  <input type="file" name="file" />
  <button class="button">Импортировать</button>
  {{ Form::close() }}
</div>
@endsection

@section('title-content')
{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
    <div class="top-bar head-content">
      <div class="top-bar-left">
        <h2 class="header-content">{{ $page_info->title }}</h2>
        <a class="icon-add sprite" data-open="first-add"></a>
      </div>
      <div class="top-bar-right">
        @if (isset($filter))
        <a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
        @endif
        <input class="search-field" type="search" name="search_field" placeholder="Поиск" />
        <button type="button" class="icon-search sprite button"></button>
      </div>
    </div>
    {{-- Блок фильтров --}}
    @if (isset($filter))

    {{-- Подключаем класс Checkboxer --}}
    @include('includes.scripts.class.checkboxer')

    <div class="grid-x">
      <div class="small-12 cell filters fieldset-filters" id="filters">
        <div class="grid-padding-x">
          <div class="small-12 cell text-right">
            {{ link_to(Request::url(), 'Сбросить', ['class' => 'small-link']) }}
          </div>
        </div>
        <div class="grid-padding-x">
          <div class="small-12 cell">
            {{ Form::open(['url' => Request::url(), 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

            @include($page_info->alias.'.filters')

            <div class="small-12 cell text-center">
              {{ Form::submit('Фильтрация', ['class'=>'button']) }}
            </div>
            {{ Form::close() }}
          </div>
        </div>
        <div class="grid-x">
          <a class="small-12 cell text-center filter-close">
            <button type="button" class="icon-moveup sprite"></button>
          </a>
        </div>
      </div>
    </div>

    @endif
  </div>
</div>
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="services">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-photo">Фото</th>
          <th class="td-name">Название артикула</th>
          <th class="td-service">Услуга</th>
          <th class="td-company-id">Компания</th>
          <th class="td-author">Автор</th>
          @can ('publisher', App\Service::class)
          <th class="td-display">Отображение</th>
          @endcan
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($services))

        @foreach($services as $service)
        <tr class="item @if($service->moderation == 1)no-moderation @endif" id="services-{{ $service->id }}" data-name="{{ $service->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="album_id" id="check-{{ $service->id }}"
            {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
            @if(!empty($filter['booklist']['booklists']['default']))
            {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
            @if (in_array($service->id, $filter['booklist']['booklists']['default'])) checked 
            @endif
            @endif
            >
            <label class="label-check" for="check-{{ $service->id }}"></label>
          </td>
          <td>
            <a href="/admin/services/{{ $service->id }}/edit">
              <img src="{{ isset($service->photo_id) ? '/storage/'.$service->company_id.'/media/services/'.$service->id.'/img/small/'.$service->photo->name : '/crm/img/plug/service_small_default_color.jpg' }}" alt="{{ isset($service->photo_id) ? $service->name : 'Нет фото' }}">
            </a>
          </td>
          <td class="td-name"><a href="/admin/services/{{ $service->id }}/edit">{{ $service->name }}</a></td>
          <td class="td-service">{{ $service->services_product->name }}</td>
          <td class="td-company-id">@if(!empty($service->company->name)) {{ $service->company->name }} @else @if($service->system_item == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-author">@if(isset($service->author->first_name)) {{ $service->author->first_name . ' ' . $service->author->second_name }} @endif</td>
          @can ('publisher', $service)
          <td class="td-display">
            @if ($service['display'] == 1)
            <a class="icon-display-show black sprite" data-open="item-display"></a>
            @else
            <a class="icon-display-hide black sprite" data-open="item-display"></a>
            @endif
          </td>
          @endcan
          <td class="td-delete">
            @if ($service->system_item != 1)
            @can('delete', $service)
            <a class="icon-delete sprite" data-open="item-delete"></a>
            @endcan
            @endif
          </td>       
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
    <span class="pagination-title">Кол-во записей: {{ $services->count() }}</span>
    {{ $services->links() }}
  </div>
</div>
@endsection

@section('modals')
<section id="modal"></section>
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')

@endsection

@section('scripts')

{{-- Скрипт отображеняи на сайте --}}
@include('includes.scripts.display-ajax')

<script type="text/javascript">


  // Обозначаем таймер для проверки
  var timerId;
  var time = 400;

  // Первая буква заглавная
  function newParagraph (name) {
    name = name.charAt(0).toUpperCase() + name.substr(1).toLowerCase();
    return name;
  };

    // ------------------- Проверка на совпадение имени --------------------------------------
    function serviceCheck (name, submit, db) {

    // Блокируем аттрибут базы данных
    $(db).val(0);

    // Смотрим сколько символов
    var lenname = name.length;

      // Если символов больше 3 - делаем запрос
      if (lenname > 3) {

        // Первая буква сектора заглавная
        name = newParagraph (name);

        // Сам ajax запрос
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "/admin/service_check",
          type: "POST",
          data: {name: name},
          beforeSend: function () {
            $('.find-status').addClass('icon-load');
          },
          success: function(date){
            $('.find-status').removeClass('icon-load');
            var result = $.parseJSON(date);
            // Если ошибка
            if (result.error_status == 1) {
              $(submit).prop('disabled', true);
              $('.item-error').css('display', 'block');
              $(db).val(0);
            } else {
              // Выводим пришедшие данные на страницу
              $(submit).prop('disabled', false);
              $('.item-error').css('display', 'none');
              $(db).val(1);
            };
          }
        });
      };
      // Удаляем все значения, если символов меньше 3х
      if (lenname <= 3) {
        $(submit).prop('disabled', false);
        $('.item-error').css('display', 'none');
        $(db).val(0);
      };
    };

    // ---------------------------- Продукция -----------------------------------------------

    // ----------- Добавление -------------
    // Открываем модалку
    $(document).on('click', '[data-open="first-add"]', function() {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/services/create',
        type: "GET",
        success: function(html){
          $('#modal').html(html);
          $('#first-add').foundation();
          $('#first-add').foundation('open');
        }
      }); 
    });



    // Проверка существования
    $(document).on('keyup', '#form-first-add .name-field', function() {

      // Получаем фрагмент текста
      var name = $('#form-first-add .name-field').val();

      // Указываем название кнопки
      var submit = '.modal-button';

      // Значение поля с разрешением
      var db = '#form-first-add .first-item';

      // Выполняем запрос
      clearTimeout(timerId);   
      timerId = setTimeout(function() {
        serviceCheck (name, submit, db)
      }, time); 
    });
  </script>
  {{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
  @include('includes.scripts.tablesorter-script')

  {{-- Скрипт чекбоксов --}}
  @include('includes.scripts.checkbox-control')

  {{-- Скрипт модалки удаления --}}
  @include('includes.scripts.modal-delete-script')
  @include('includes.scripts.delete-ajax-script')
  @include('includes.scripts.sortable-table-script')
  @endsection
