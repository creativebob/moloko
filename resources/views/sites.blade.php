@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
  @include('includes.table-inhead')
@endsection

@section('title')
  {{ $page_info->page_name }}
@endsection

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
	  <div class="top-bar head-content">
	    <div class="top-bar-left">
	      <h2 class="header-content">{{ $page_info->page_name }}</h2>
	      <a class="icon-add sprite" data-open="site-add"></a>
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
          <th class="td-site-name">Название сайта</th>
          <th class="td-site-domen">Домен сайта</th>
          <th class="td-company-name">Компания</th>
          <th class="td-site-edit">Изменить</th>
          <th class="td-site-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($sites))
        @foreach($sites as $site)
        <tr class="parent" id="sites-{{ $site->id }}" data-name="{{ $site->site_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $site->id }}"><label class="label-check" for="check-{{ $site->id }}"></label></td>
          <td class="td-site-name"><a href="/sites/{{ $site->site_alias }}">{{ $site->site_name }}</a></td>
          <td class="td-site-domen"><a href="http://{{ $site->site_domen }}" target="_blank">{{ $site->site_domen }}</a></td>
          <td class="td-company-name" data-company-id="{{ $site->company->id or '' }}">{{ $site->company->company_name or 'Системный сайт' }}</td>
          <td class="td-site-edit"><a class="tiny button" data-open="site-edit">Редактировать</a></td>
          <td class="td-site-author">@if(isset($site->author->first_name)) {{ $site->author->first_name . ' ' . $site->author->second_name }} @endif</td>
          <td class="td-delete">
            @if (isset($site->company_id))
            <a class="icon-delete sprite" data-open="item-delete"></a>
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
    <span class="pagination-title">Кол-во записей: {{ $sites->count() }}</span>
    {{ $sites->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка добавления сайта --}}
<div class="reveal" id="site-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ сайта</h5>
    </div>
  </div>
  {{ Form::open(['url'=>'/sites', 'id' => 'form-site-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x modal-content inputs align-center">
      <div class="small-10 medium-8 cell">
        <label>Название сайта
           {{ Form::text('site_name', $value = null, ['autocomplete'=>'off', 'required']) }}
           <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        <label>Домен сайта
           {{ Form::text('site_domen', $value = null, ['autocomplete'=>'off']) }}
        </label>
      </div>
      <div class="small-6 medium-4 cell">
        <fieldset class="fieldset-access">
          <legend>Разделы сайта</legend>
          <ul>
            @foreach ($sections as $section)
            <li class="checkbox"> 
              {{ Form::checkbox('sections[]', $section->id, null, ['id'=>'section-'.$section->id, 'checked']) }}
              <label for="section-{{ $section->id }}"><span>{{ $section->menu_name }}</span></label>
            </li>
            @endforeach
          </ul>
        </fieldset> 
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления сайта --}}

{{-- Модалка редактирования сайта --}}
<div class="reveal" id="site-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>редактирование сайта</h5>
    </div>
  </div>
  {{ Form::model(['id'=>'form-site-edit', 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-8 cell">
        <label>Название сайта
          {{ Form::text('site_name', $value = null, ['id'=>'site-name-field', 'autocomplete'=>'off', 'required']) }}
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        <label>Домен сайта
          {{ Form::text('site_domen', $value = null, ['id'=>'site-domen-field', 'autocomplete'=>'off']) }}
        </label>
      </div>
      <div class="small-6 medium-4 cell">
        <fieldset class="fieldset-access">
          <legend>Разделы сайта</legend>
          <ul>
            @foreach ($sections as $section)
            <li class="checkbox"> 
              {{ Form::checkbox('sections[]', $section->id, null, ['id'=>'section-'.$section->id]) }}
              <label for="section-{{ $section->id }}"><span>{{ $section->menu_name }}</span></label>
            </li>
            @endforeach
          </ul>
        </fieldset> 
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
       {{ Form::submit('Сохранить', ['class'=>'button modal-button']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки редактирования сайта --}}

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
<script type="text/javascript">
  $(function() {
    $(document).on('click', '[data-open="site-edit"]', function() {
      // При клике на редактирование сайта в модальном окне заполняем инпуты
      var parent = $(this).closest('.parent');
      var type = parent.attr('id').split('-')[0];
      var id = parent.attr('id').split('-')[1];
      var name = parent.find('.td-site-name').text();
      var domen = parent.find('.td-site-domen').text();
      var company = parent.find('.td-company-name').data('company-id');

      $('#form-site-edit').attr('action', '/' + type + '/' + id);
      $('#site-name-field').val(name);
      $('#site-domen-field').val(domen);
      $('#site-company-select>[value="' + company + '"]').prop('selected', true);

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/menusite/" + id,
        type: "GET",
        success: function(date){
          // var result = $.parseJSON(date);
          alert(date);
          // if (result.error_status == 0) {
          //   data = "<tr><td>Данный отдел уже сущестует в этой компании!</td></tr>";
          //   // Выводим пришедшие данные на страницу
          //   $('#tbody-department-add').append(data);
          // };
          // if (result.error_status == 1) {
          //   $('#department-database').val(1);
          //   $('#submit-department-add').prop('disabled', false);
          // };
        }
      });
    });
  });
</script>



{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.modals.modal-delete-script')
@endsection

