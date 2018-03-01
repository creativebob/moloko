@extends('layouts.app')
 
@section('inhead')
  <meta name="description" content="{{ $page_info->page_description }}" />
@endsection

@section('title', $page_info->page_name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-options="stickyOn: small;" data-top-anchor="head-content:top">
    <div class="top-bar head-content">
      <div class="top-bar-left">
        <h2 class="header-content">{{ $page_info->page_name }}</h2>
        @can('create', App\Sector::class)
        <a class="icon-add sprite" data-open="industry-add"></a>
        @endcan
      </div>
      <div class="top-bar-right">
        <a class="icon-filter sprite"></a>
        <input class="search-field" type="search" name="search-field" placeholder="Поиск" />
        <button type="button" class="icon-search sprite button"></button>
      </div>
    </div>
    {{-- Блок фильтров --}}
    <div class="grid-x">
      <div class="small-12 cell filters" id="filters">
        <fieldset class="fieldset-filters inputs">
          {{ Form::open(['data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}
          <legend>Фильтрация</legend>
          <div class="grid-x grid-padding-x"> 
            <div class="small-6 cell">
              <label>Статус пользователя
                {{ Form::select('user_type', [ 'all' => 'Все пользователи','1' => 'Сотрудник', '2' => 'Клиент'], 'all') }}
              </label>
            </div>
            <div class="small-6 cell">
              <label>Блокировка доступа
                {{ Form::select('access_block', [ 'all' => 'Все пользователи', '1' => 'Доступ блокирован', '' => 'Доступ открыт'], 'all') }}
              </label>
            </div>

            <div class="small-12 medium-12 align-center cell tabs-button">
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
{{-- Список --}}
<div class="grid-x">
  <div class="small-12 cell">

    @if($sectors_tree)
      <ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
        @foreach ($sectors_tree as $sector)
         
          @if($sector['industry_status'] == 1)
            {{-- Если индустрия --}}
            <li class="first-item parent 
            @if (isset($sector['children'])))
            parent-item
            @endif" id="sectors-{{ $sector['id'] }}" data-name="{{ $sector['sector_name'] }}">
              <ul class="icon-list">
                <li>
                  @can('create', App\Sector::class)
                  <div class="icon-list-add sprite" data-open="sector-add"></div>
                  @endcan
                </li>
                <li>
                  @if($sector['edit'] == 1)
                  <div class="icon-list-edit sprite" data-open="industry-edit"></div>
                  @endif
                </li>
                <li>
                  @if (!isset($sector['children']) && ($sector['system_item'] != 1) && $sector['delete'] == 1)
                    <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
                  @endif
                </li>
              </ul>
              <a data-list="" class="first-link">
                <div class="list-title">
                  <div class="icon-open sprite"></div>
                  <span class="first-item-name">{{ $sector['sector_name'] }}</span>
                  <span class="number">{{ $sector['count'] }}</span>
                </div>
              </a>
            @if (isset($sector['children']))
              <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
                  @foreach($sector['children'] as $sector)
                    @include('sectors.sectors-list', $sector)
                  @endforeach
              </ul>
            @endif
          </li>
          @endif
        @endforeach
      </ul>
    @endif
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка добавления индустрии --}}
<div class="reveal" id="industry-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ индустрии</h5>
    </div>
  </div>
  
  <form id="form-industry-add" data-abide novalidate method="POST">
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название индустрии
          @include('includes.inputs.name', ['value'=>null, 'name'=>'industry_name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такая индустрия уже существует!</div>
        </label>
        <input type="hidden" name="industry_db" class="industry-db" value="0" pattern="[0-9]{1}">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-industry-add', 'data-close', 'disabled']) }}
      </div>
    </div>
  </form>
  {{-- Form::open(['id'=>'form-industry-add', 'data-abide', 'novalidate']) --}}
  {{-- Form::close() --}}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления индустрии --}}

{{-- Модалка редактирования индустрии --}}
<div class="reveal" id="industry-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование индустрии</h5>
    </div>
  </div>
  <form id="form-industry-edit" data-abide novalidate>
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название индустрии
          @include('includes.inputs.name', ['value'=>null, 'name'=>'industry_name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такая индустрия уже существует!</div>
        </label>
        <input type="hidden" name="industry_id" class="industry-id">
        <input type="hidden" name="industry_db" class="industry-db" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-industry-edit', 'data-close', 'disabled']) }}
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки редактирования индустрии --}}

{{-- Модалка добавления сектора --}}
<div class="reveal" id="sector-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ сектора</h5>
    </div>
  </div>
  <!-- Добавляем сектор -->
  <form id="form-sector-add" data-abide novalidate method="POST">
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название сектора
          @include('includes.inputs.name', ['value'=>null, 'name'=>'sector_name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такой сектор уже существует!</div>
        </label>
        <input type="hidden" name="sector_parent_id" class="sector-parent-id-field">
        <input type="hidden" name="industry_id" class="industry-id-field">
        <input type="hidden" name="sector_db" class="sector-db" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-sector-add', 'disabled']) }}
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления сектора --}}

{{-- Модалка редактирования сектора --}}
<div class="reveal" id="sector-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование сектора</h5>
    </div>
  </div>
  <!-- Редактируем отдел -->
  <form id="form-sector-edit" data-abide novalidate>
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Расположение
          @include('includes.inputs.sector', ['sector_id'=>null, 'name'=>'industry_id'])
        </label>
        <label>Название сектора
          @include('includes.inputs.name', ['value'=>null, 'name'=>'sector_name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такой сектор уже существует!</div>
        </label>
        <input type="hidden" name="sector_parent_id" class="sector-parent-id-field">
        <input type="hidden" name="sector_db" class="sector-db" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-sector-edit', 'disabled']) }}
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки сектора --}}

{{-- Модалка удаления ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
<script type="text/javascript">
$(function() {
  // Функция появления окна с ошибкой
  function showError (msg) {
    var error = "<div class=\"callout item-error\" data-closable><p>" + msg + "</p><button class=\"close-button error-close\" aria-label=\"Dismiss alert\" type=\"button\" data-close><span aria-hidden=\"true\">&times;</span></button></div>";
    return error;
  };

  // Добавляем индустрию
  $(document).on('click', '#submit-industry-add', function(event) {

    // Блочим отправку формы
    event.preventDefault();

    // Получаем данные
    var sector = $('#form-industry-add .name-field').val();
    var industry_db = $('#form-industry-add .industry-db').val();

    // Первая буква сектора заглавная
    sector = sector.charAt(0).toUpperCase() + sector.substr(1);
    
    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors",
      type: "POST",
      data: {name: sector, first_item: industry_db},
      success: function(date){
        var result = $.parseJSON(date);
        if (result.error_status == 0) {
          var data = '<li class="first-item parent" id=\"sectors-'+ result.id +'\" data-name=\"'+ result.name +'\"><ul class="icon-list"><li>';

          if (result.create == 1) {
            data = data + '<div class=\"icon-list-add sprite\" data-open=\"sector-add\"></div>';
          };
          data = data + '</li><li>';
          if (result.edit == 1) {
            data = data + '<div class=\"icon-list-edit sprite\" data-open=\"industry-edit\"></div>';
          };
          data = data + '</li><li>';
          if (result.delete == 1) {
            data = data + '<div class=\"icon-list-delete sprite\" data-open=\"item-delete-ajax\"></div>';
          };
          data = data + '</li></ul><a data-list="" class=\"first-link\"><div class=\"list-title\"><div class=\"icon-open sprite\"></div><span class=\"first-item-name\">' + result.name + '</span><span class=\"number\">0</span></div></a></li>';

          $('.content-list').append(data);
        } else {
          var error = showError (result.error_message);
          $('#form-industry-add .name-field').after(error);
        }
      }
    });
  });

  // Редактируем индустрию
  // Открываем модалку
  $(document).on('click', '[data-open="industry-edit"]', function() {

    // Блокируем кнопку
    $('.submit-industry-edit').prop('disabled', false);

    // Получаем данные о филиале
    var id = $(this).closest('.parent').attr('id').split('-')[1];

    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors/" + id + "/edit",
      type: "GET",
      success: function(date){
        var result = $.parseJSON(date);
        $('#form-industry-edit .name-field').val(result.name);
        $('#form-industry-edit .industry-id').val(result.id);
        $('#form-industry-edit .industry-db').val(1);
      }
    });
  });

  // Меняем данные индустрии
  $(document).on('click', '#submit-industry-edit', function(event) {

    // Блочим отправку формы
    event.preventDefault();

    // Получаем данные
    var id = $('#form-industry-edit .industry-id').val();
    var sector = $('#form-industry-edit .name-field').val();
    var industry_db = $('#form-industry-edit .industry-db').val();

    // Первая буква сектора заглавная
    sector = sector.charAt(0).toUpperCase() + sector.substr(1);
    
    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors/" + id,
      type: "PATCH",
      data: {name: sector, first_item: industry_db},
      success: function(date){
        var result = $.parseJSON(date);

        // alert($('#sectors-' + result.id + ' .first-item-name').text('дщд'));

        if (result.error_status == 0) {
          $('#sectors-' + result.id + ' .first-item-name').text(result.name);
          $('#sectors-' + result.id).data('name', result.name);
        } else {
          var error = showError (result.error_message);
          $('#form-industry-add .name-field').after(error);
        }
      }
    });
  });
  // Добавление сектора
  $(document).on('click', '[data-open="sector-add"]', function() {
    var parent = $(this).closest('.parent').attr('id').split('-')[1];
    var industry = $(this).closest('.first-item').attr('id').split('-')[1];
    $('#form-sector-add .industry-id-field').val(industry);
    $('#form-sector-add .sector-parent-id-field').val(parent);
  });
  // Редактируем сектор
  $(document).on('click', '[data-open="sector-edit"]', function() {
    var parent = $(this).closest('.parent').attr('id').split('-')[1];
    var industry = $(this).closest('.first-item').attr('id').split('-')[1];
    $('.industry-id-field').val(industry);
    var id = $(this).closest('.parent').attr('id').split('-')[1];
    // Блокируем кнопку
    $('#submit-sector-edit').prop('disabled', false);
    // Получаем данные о филиале
    $('#form-sector-edit').attr('action', '/sectors/' + id);
    // Сам ajax запрос
    // alert(id);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors/" + id + "/edit",
      type: "GET",
      success: function(date){
        var result = $.parseJSON(date);
        $('#form-sector-edit .name-field').val(result.sector_name);
        $('#form-sector-edit .sector-db').val(1);
        $('#form-sector-edit option[value="' + result.industry_id + '"]').attr("selected", "selected");
        $('#form-sector-edit .sector-parent-id-field').val(result.sector_parent_id);
      }
    });
  });

  // Чекаем название в нашей бд
  function sectorCheck (sector, submit, db) {
    // Блокируем кнопку
    $(submit).prop('disabled', true);
    $(db).val(0);

    // Первая буква сектора заглавная
    sector = sector.charAt(0).toUpperCase() + sector.substr(1);

    // Смотрим сколько символов
    var lensector = sector.length;

    // Если символов больше 3 - делаем запрос
    if (lensector > 3) {

      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/sector_check",
        type: "POST",
        data: {sector_name: sector},
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
    if (lensector <= 3) {
      // Удаляем все значения, если символов меньше 3х
      $(submit).prop('disabled', true);
      $('.item-error').css('display', 'none');
      $(db).val(0);
    };
  };

  // Добаление индустрии
  $('#form-industry-add .name-field').keyup(function() {
    // Получаем фрагмент текста
    var industry = $('#form-industry-add .name-field').val();
    // Указываем название кнопки
    var submit = '#submit-industry-add';
    // Значение поля с разрешением
    var db = '#form-industry-add .industry-db';
    // Передаем в фугкцию переменные
    sectorCheck (industry, submit, db);
  });

  // Изменение индустрии
  $('#form-industry-edit .name-field').keyup(function() {
    // Получаем фрагмент текста
    var industry = $('#form-industry-edit .name-field').val();
    // Указываем название кнопки
    var submit = '#submit-industry-edit';
    // Значение поля с разрешением
    var db = '#form-industry-edit .industry-db';
    // Передаем в фугкцию переменные
    sectorCheck (industry, submit, db);
  });

  // Добаление сектора
  $('#form-sector-add .name-field').keyup(function() {
    // Получаем фрагмент текста
    var sector = $('#form-sector-add .name-field').val();
    // Указываем название кнопки
    var submit = '#submit-sector-add';
    // Значение поля с разрешением
    var db = '#form-sector-add .sector-db';
    // Передаем в фугкцию переменные
    sectorCheck (sector, submit, db);
  });

  // Изменение сектора
  $('#form-sector-edit .name-field').keyup(function() {
    // Получаем фрагмент текста
    var sector = $('#form-sector-edit .name-field').val();
    // Указываем название кнопки
    var submit = '#submit-sector-edit';
    // Значение поля с разрешением
    var db = '#form-sector-edit .sector-db';
    // Передаем в фугкцию переменные
    sectorCheck (sector, submit, db);
  });

  // При закрытии модалки очищаем поля
  $(document).on('click', '.close-modal', function() {
    $('.name-field').val('');
    $('.industry-db').val(0);
    $('.sector-db').val(0);
    $('.item-error').css('display', 'none');
    $('#sectors-select>option').each(function(i,elem) {
      $(elem).removeAttr('selected');
    });
  });

  // Открываем меню и подменю, если только что добавили населенный пункт
  @if(!empty($data))
    // Общие правила
    // Подсвечиваем Филиал
    $('#sectors-{{ $data['section_id'] }}').addClass('first-active').find('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
    // Отображаем отдел и филиал, без должностей
    if ({{ $data['item_id'] }} == 0) {
      var firstItem = $('#sectors-{{ $data['section_id'] }}').find('.medium-list:first');
      // Открываем аккордион
      $('#content-list').foundation('down', firstItem); 
    } else {
      // Перебираем родителей и подсвечиваем их
      $.each($('#sectors-{{ $data['item_id'] }}').parents('.parent-item').get().reverse(), function (index) {
        $(this).children('.medium-link:first').addClass('medium-active');
        $(this).children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
        $('#content-list').foundation('down', $(this).closest('.medium-list'));
      });
      // Если родитель содержит не пустой элемент
      if ($('#sectors-{{ $data['item_id'] }}').parent('.parent').has('.parent-item')) {
        $('#content-list').foundation('down', $('#sectors-{{ $data['item_id'] }}').closest('.medium-list'));
      };
      // Если элемент содержит вложенность, открываем его
      if ($('#sectors-{{ $data['item_id'] }}').hasClass('parent')) {
        $('#sectors-{{ $data['item_id'] }}').children('.medium-link:first').addClass('medium-active');
        $('#sectors-{{ $data['item_id'] }}').children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
        $('#content-list').foundation('down', $('#sectors-{{ $data['item_id'] }}').children('.medium-list:first'));
      }
    };
  @endif
});
</script>
{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')
{{-- Скрипт модалки удаления ajax --}}
@include('includes.scripts.modal-delete-ajax-script')
@endsection