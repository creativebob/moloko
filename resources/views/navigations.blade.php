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
	      <a class="icon-add sprite" data-open="navigation-add"></a>
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
          <th class="td-navigation-name">Название навигации</th>
          <th class="td-navigation-site">Cайт</th>
          <th class="td-navigation-edit">Редактирование</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($navigations))
        @foreach($navigations as $navigation)
        <tr class="parent" id="navigations-{{ $navigation->id }}" data-name="{{ $navigation->navigation_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $navigation->id }}"><label class="label-check" for="check-{{ $navigation->id }}"></label></td>
          <td class="td-navigation-name"><a href="/menus?site_id={{ $navigation->site->id }}">{{ $navigation->navigation_name }}</a></td>
          <td class="td-navigation-site" data-site-id="{{ $navigation->site->id or '' }}">{{ $navigation->site->site_name }}</td>
          <td class="td-navigation-edit"><a class="icon-edit sprite" data-open="navigation-edit"></a></td>
          <td class="td-delete">
            @if ($navigation->system_item == null)
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
    <span class="pagination-title">Кол-во записей: {{ $navigations->count() }}</span>
    {{ $navigations->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка добавления навигации --}}
<div class="reveal" id="navigation-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ навигации</h5>
    </div>
  </div>
  {{ Form::open(['url'=>'/navigations', 'id' => 'form-navigation-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Сайт навигации
          {{ Form::select('site_id', $sites, null, ['id'=>'add-site-select']) }}
        </label>
        <label>Название навигации
           {{ Form::text('navigation_name', $value = null, ['autocomplete'=>'off', 'required']) }}
           <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
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
{{-- Конец модалки добавления навигации --}}

{{-- Модалка редактирования навигации --}}
<div class="reveal" id="navigation-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>редактирование навигации</h5>
    </div>
  </div>
  {{ Form::open(['id'=>'form-navigation-edit', 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Сайт навигации
          {{ Form::select('site_id', $sites, null, ['id'=>'edit-site-select']) }}
        </label>
        <label>Название навигации
          {{ Form::text('navigation_name', $value = null, ['id'=>'navigation-name-field', 'autocomplete'=>'off', 'required']) }}
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
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
{{-- Конец модалки редактирования навигации --}}

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
<script type="text/javascript">
  $(function() {
    // При клике на редактирование сайта в модальном окне заполняем инпуты
    $(document).on('click', '[data-open="navigation-edit"]', function() {
      var parent = $(this).closest('.parent');
      var type = parent.attr('id').split('-')[0];
      var id = parent.attr('id').split('-')[1];
      var name = parent.find('.td-navigation-name').text();
      var site = parent.find('.td-navigation-site').data('site-id');
      $('#form-navigation-edit').attr('action', '/' + type + '/' + id);
      $('#navigation-name-field').val(name);
      $('#edit-site-select>[value="' + site + '"]').prop('selected', true);
    });
  });
</script>



{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.modals.modal-delete-script')
@endsection

