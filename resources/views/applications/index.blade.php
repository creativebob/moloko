@extends('layouts.app')

@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($applications))
    {{ num_format($applications->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Application::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter applications" id="content" data-sticky-container data-entity-alias="applications">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-number">Номер заявки</th>
          <th class="td-date">Отправлена</th>
          <th class="td-description">Заявка</th>
          <th class="td-supplier-name">Поставщик</th>
          <th class="td-phone">Телефон</th>
          <th class="td-amount">Сумма</th>
          <th class="td-payment">Оплачено</th>
          <th class="td-stage">Этап</th>
          <th class="td-created_at">Создана</th>
          <th class="td-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($applications))
        @foreach($applications as $application)
          <tr class="item @if($application->moderation == 1)no-moderation @endif" id="applications-{{ $application->id }}" data-name="{{ $application->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">

            <input type="checkbox" class="table-check" name="application" id="check-{{ $application->id }}"
            @if(!empty($filter['booklist']['booklists']['default']))
            @if (in_array($application->id, $filter['booklist']['booklists']['default'])) checked
            @endif
            @endif
            >
            <label class="label-check" for="check-{{ $application->id }}"></label>

          </td>

          <td class="td-number">
                {{ $application->number or '' }}
            <br><span class="tiny-text"></span>
          </td>

          <td class="td-send-date">
            <span>{{ isset($application->send_date) ? $application->send_date->format('d.m.Y') : null }}</span><br>
            <span class="tiny-text">{{ isset($application->send_date) ? $application->send_date->format('H:i') : null }}</span>
          </td>

          <td class="td-description">

            @can('view', $application)
                <a href="/admin/applications/{{ $application->id }}/edit">
                <span data-toggle="dropdown-{{ $application->id }}">{{ $application->name or '' }}</span></a>
                <div class="dropdown-pane bottom right" id="dropdown-{{ $application->id }}" data-dropdown data-hover="true" data-hover-pane="true">
                  {!! $application->description or '' !!}
                </div>
            @else
                {{ $application->name or '' }}
            @endcan

          </td>

          <td class="td-supplier-name">

          <a href="/admin/applications?supplier_id%5B%5D={{ $application->supplier->id }}" class="filter_link" title="Фильтровать">
            {{ $application->supplier->name }}
          </a>
          <br>
          <span class="tiny-text">
            {{ $application->supplier->location->city->name }}, {{ $application->supplier->location->address }}
          </span>
          <td class="td-phone">
            {{ isset($application->supplier->main_phone->phone) ? decorPhone($application->supplier->main_phone->phone) : 'Номер не указан' }}
            @if($application->supplier->email)<br><span class="tiny-text">{{ $application->supplier->email or '' }}</span>@endif
          </td>

          <td class="td-amount">{{ num_format($application->amount, 0) }}</td>
          <td class="td-payment">{{ num_format($application->payment, 0) }}
          <br><span class="tiny-text">{{ num_format($application->amount - $application->payment, 0) }}</span>
          </td>
          <td class="td-stage">{{ $application->stage->name or '' }}</td>
          <td class="td-created_at">
            <span>{{ $application->created_at->format('d.m.Y') }}</span><br>
            <span class="tiny-text">{{ $application->created_at->format('H:i') }}</span>
          </td>
          <td class="td-author">{{ $application->author->name or '' }}</td>
          <td class="td-delete">
          @if ($application->system_item !== 1)
            @can('delete', $application)
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
    <span class="pagination-title">Кол-во записей: {{ $applications->count() }}</span>
    {{ $applications->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
  </div>
</div>

@endsection

@section('modals')
<section id="modal"></section>

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

@endsection

@section('scripts')

    {{-- Скрипт сортировки и перетаскивания для таблицы --}}
    @include('includes.scripts.tablesorter-script')
    @include('includes.scripts.sortable-table-script')
    @include('includes.scripts.pickmeup-script')

    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')

    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')

    {{-- Скрипт чекбоксов --}}
    @include('includes.scripts.checkbox-control')

    {{-- Скрипт модалки удаления --}}
    @include('includes.scripts.modal-delete-script')
    @include('includes.scripts.delete-ajax-script')

@endsection

