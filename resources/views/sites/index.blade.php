@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
  {{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($sites))
    {{ num_format($sites->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Site::class, 'type' => 'table'])
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="sites">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Название сайта</th>
          <th class="td-domain">Домен сайта</th>
          <th class="td-api-token">Api токен</th>
          <th class="td-company-name">Компания</th>
          <th class="td-edit">Изменить</th>
          <th class="td-author">Автор</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($sites))
        @foreach($sites as $site)
        @php
          $edit = 0;
        @endphp
        @can('update', $site)
          @php
            $edit = 1;
          @endphp
        @endcan
        <tr class="item @if($site->moderation == 1)no-moderation @endif" id="sites-{{ $site->id }}" data-name="{{ $site->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $site->id }}"

              {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
              @if(!empty($filter['booklist']['booklists']['default']))
                {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                @if (in_array($site->id, $filter['booklist']['booklists']['default'])) checked 
              @endif
            @endif

            >
            <label class="label-check" for="check-{{ $site->id }}"></label></td>
          <td class="td-name">
            @if($edit == 1)
              <a href="/admin/sites/{{ $site->alias }}">
            @endif
            {{ $site->name }}
            @if($edit == 1)
              </a> 
            @endif
          </td>
          <td class="td-domain"><a href="http://{{ $site->domain }}" target="_blank">{{ $site->domain }}</a></td>
          <td class="td-api-token">{{ $site->api_token }}</td>
          <td class="td-company-id">@if(!empty($site->company->name)) {{ $site->company->name }} @else @if($site->system_item == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-edit">
            @if($edit == 1)
            <a class="tiny button" href="/admin/sites/{{ $site->alias }}/edit">Редактировать</a>
            @endif
          </td>
          <td class="td-author">@if(isset($site->author->first_name)) {{ $site->author->first_name . ' ' . $site->author->second_name }} @endif</td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $site])

          <td class="td-delete">
            @if ($site->system_item != 1)
              @can('delete', $site)
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
    <span class="pagination-title">Кол-во записей: {{ $sites->count() }}</span>
    {{ $sites->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection