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
            {{-- Если категория --}}
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
                    <div class="icon-list-delete sprite" data-open="item-delete"></div>
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
{{-- Модалка добавления категории --}}
<div class="reveal" id="industry-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ категории</h5>
    </div>
  </div>
  {{ Form::open(['url'=>'/sectors', 'id' => 'form-industry-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название категории
          @include('includes.inputs.name', ['value'=>null, 'name'=>'industry_name'])
        </label>
        <input type="hidden" name="industry_db" class="industry-db" value="0" pattern="[0-9]{1}">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-industry-add']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления категории --}}

{{-- Модалка редактирования категории --}}
<div class="reveal" id="industry-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование категории</h5>
    </div>
  </div>
  {{ Form::open([ 'data-abide', 'novalidate', 'id'=>'form-industry-edit']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название категории
          @include('includes.inputs.name', ['value'=>null, 'name'=>'industry_name'])
        </label>
        <input type="hidden" name="industry_db" class="industry-db" value="1">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-industry-edit']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки редактирования категории --}}

{{-- Модалка добавления сектора --}}
<div class="reveal" id="sector-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ сектора</h5>
    </div>
  </div>
  <!-- Редактируем отдел -->
  {{ Form::open(['id' => 'form-sector-add', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название сектора
          @include('includes.inputs.name', ['value'=>null, 'name'=>'sector_name'])
          <div class="sector-error">Данный отдел уже существует в этом филиале!</div>
        </label>
        <input type="hidden" name="sector_parent_id" class="sector-parent-id-field">
        <input type="hidden" name="industry_id" class="industry-id-field">
        <input type="hidden" name="sector_db" class="sector-db" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-sector-add']) }}
      </div>
    </div>
  {{ Form::close() }}
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
  {{ Form::open(['id' => 'form-sector-edit', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название сектора
          @include('includes.inputs.name', ['value'=>null, 'name'=>'sector_name'])
          <div class="sector-error">Данный отдел уже существует в этом филиале!</div>
        </label>
        <input type="hidden" name="sector_parent_id" class="sector-parent-id-field">
        <input type="hidden" name="industry_id" class="industry-id-field">
        <input type="hidden" name="sector_db" class="sector-db" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-sector-edit']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки сектора --}}

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
<script type="text/javascript">
$(function() {
  // Редактируем филиал
  $(document).on('click', '[data-open="industry-edit"]', function() {
    // Блокируем кнопку
    $('.submit-industry-edit').prop('disabled', false);
    // Получаем данные о филиале
    var id = $(this).closest('.parent').attr('id').split('-')[1];
    $('#form-industry-edit').attr('action', '/sectors/' + id);
    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors/" + id + "/edit",
      type: "GET",
      success: function(date){
        var result = $.parseJSON(date);
        $('#form-industry-edit .city-check-field').val(result.city_name);
        $('#form-industry-edit .city-id-field').val(result.city_id);
        $('#form-industry-edit .name-field').val(result.industry_name);
        $('#form-industry-edit .address-field').val(result.industry_address);
        $('#form-industry-edit .phone-field').val(result.industry_phone);
        $('#form-industry-edit .industry-db-edit').val(1);
      }
    });
  });
  // Добавление сектора или должности
  $(document).on('click', '[data-open="sector-add"]', function() {
    var parent = $(this).closest('.parent').attr('id').split('-')[1];
    var industry = $(this).closest('.first-item').attr('id').split('-')[1];
    $('#form-sector-add .industry-id-field').val(industry);
    $('#form-sector-add .sector-parent-id-field').val(parent);
  });
  // Редактируем отдел
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
        // alert(result);
        $('#form-sector-edit .name-field').val(result.sector_name);
        $('#form-sector-edit .sector-db').val(1);
        $('#form-sector-edit .industry-id-field').val(result.industry_id);
        $('#form-sector-edit .sector-parent-id-field').val(result.sector_parent_id);
      }
    });
  });



  function sectorCheck (sector, submit) {
    // Блокируем кнопку
    $(submit).prop('disabled', true);
    $('.sector-db').val(0);
    // Первая буква сектора заглавная
    sector = sector.charAt(0).toUpperCase() + sector.substr(1);
    // Смотрим сколько символов
    var lensector = sector.length;
    // Если символов больше 3 - делаем запрос
    if (lensector > 2) {
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/sector_check",
        type: "POST",
        data: {sector_name: sector, industry_id: $('.industry-id-field').val(), sector_db: $('#form-sector-add .sector-db').val()},
        beforeSend: function () {
          $('.find-sector').addClass('icon-load');
        },
        success: function(date){
          $('.find-sector').removeClass('icon-load');
          // Удаляем все значения чтобы вписать новые
          var result = $.parseJSON(date);
          // alert(date);
          if (result.error_status == 0) {
            // Выводим пришедшие данные на страницу
            $('.sector-error').css('display', 'block');
          };
          if (result.error_status == 1) {
            $('.sector-error').css('display', 'none');
            $('.sector-db').val(1);
            $(submit).prop('disabled', false);
          };
        }
      });
    };
    if (lensector <= 2) {
      // Удаляем все значения, если символов меньше 3х
      $('.sector-error').css('display', 'none');
      $('.item-error').remove();
      // $('#city-name-field').val('');
    };
  };
  // Чекаем отдел в нашей бд
  $('#form-sector-add .name-field').keyup(function() {
    var submit = '#submit-sector-add';
    // Получаем фрагмент текста
    var sector = $('#form-sector-add .name-field').val();
    sectorCheck (sector, submit);
  });
  $('#form-sector-edit .sector-name-field').keyup(function() {
    var submit = '#submit-sector-edit';
    // Получаем фрагмент текста
    var sector = $('#form-sector-edit .name-field').val();
    sectorCheck (sector, submit);
  });

  // При закрытии модалки очищаем поля
  $(document).on('click', '.close-modal', function() {
    $('.name-field').val('');
    $('.table-over').remove();
    $('.sector-error').css('display', 'none');
    $('.find-status').removeClass('icon-find-ok');
    $('.find-status').removeClass('icon-find-no');
    $('.find-status').removeClass('sprite-16');
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
{{-- Скрипт модалки удаления ajax --}}
@include('includes.scripts.modal-delete-script')
@endsection