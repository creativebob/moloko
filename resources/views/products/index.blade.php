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
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="products">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-photo">Фото</th>
          <th class="td-name">Название товара</th>
          <th class="td-category">Категория</th>
          <th class="td-company-id">Компания</th>
          <th class="td-author">Автор</th>
          @can ('publisher', App\Product::class)
          <th class="td-display">Отображение</th>
          @endcan
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($products))

        @foreach($products as $product)
        <tr class="item @if($product->moderation == 1)no-moderation @endif" id="products-{{ $product->id }}" data-name="{{ $product->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="album_id" id="check-{{ $product->id }}"
            {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
            @if(!empty($filter['booklist']['booklists']['default']))
            {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
            @if (in_array($product->id, $filter['booklist']['booklists']['default'])) checked 
            @endif
            @endif
            >
            <label class="label-check" for="check-{{ $product->id }}"></label></td>
            <td>
              <a href="/products/{{ $product->id }}/edit">
                <img src="{{ isset($product->photo_id) ? '/storage/'.$product->company_id.'/media/products/'.$product->id.'/img/small/'.$product->photo->name : '/img/plug/product_small_default_color.jpg' }}" alt="{{ isset($product->photo_id) ? $product->name : 'Нет фото' }}">
              </a>
            </td>
            <td class="td-name"><a href="/products/{{ $product->id }}/edit">{{ $product->name }}</a></td>
            <td class="td-category">{{ $product->products_category->name }}</td>
            <td class="td-company-id">@if(!empty($product->company->name)) {{ $product->company->name }} @else @if($product->system_item == null) Шаблон @else Системная @endif @endif</td>
            <td class="td-author">@if(isset($product->author->first_name)) {{ $product->author->first_name . ' ' . $product->author->second_name }} @endif</td>
            @can ('publisher', $product)
            <td class="td-display">
              @if ($product['display'] == 1)
              <span class="system-item">Отображается на сайте</span>
              @else
              <span class="no-moderation">Не отображается на сайте</span>
              @endif
            </td>
            @endcan
            <td class="td-delete">
              @if ($product->system_item != 1)
              @can('delete', $product)
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
      <span class="pagination-title">Кол-во записей: {{ $products->count() }}</span>
      {{ $products->links() }}
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
    function productCheck (name, submit, db) {

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
          url: "/product_check",
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
      url: '/products/create',
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
      productCheck (name, submit, db)
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
