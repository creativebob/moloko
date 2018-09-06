@extends('layouts.app')

@section('inhead')
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Lead::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter leads" id="content" data-sticky-container data-entity-alias="leads">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-date">Дата</th>
          <th class="td-case-number">Номер</th>
          <th class="td-name">Контакт</th>
          <th class="td-action">Действие</th>
          <th class="td-phone">Телефон</th>
          <th class="td-choice">Спрос</th>
          <th class="td-badget">Сумма сделки</th>
          <th class="td-stage">Этап</th>
          <th class="td-challenge">Задача</th>
          <th class="td-deadline_date">Дедлайн</th>
          <th class="td-manager">Менеджер</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($leads))

        @foreach($leads as $lead)
        <tr class="item @if($lead->moderation == 1)no-moderation @endif stage-{{$lead->stage->id }}" id="leads-{{ $lead->id }}" data-name="{{ $lead->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">

            <input type="checkbox" class="table-check" name="lead_id" id="check-{{ $lead->id }}"
            @if(!empty($filter['booklist']['booklists']['default']))
            @if (in_array($lead->id, $filter['booklist']['booklists']['default'])) checked 
            @endif
            @endif 
            ><label class="label-check" for="check-{{ $lead->id }}"></label>
          </td>
          <td class="td-date">
            <span>{{ $lead->created_at->format('d.m.Y') }}</span><br>
            <span class="tiny-text">{{ $lead->created_at->format('H:i') }}</span>        
          </td>

          <td class="td-case-number">{{ $lead->case_number }}</td>
          <td class="td-name">


            @can('edit', $lead)
              @can('update', $lead)
              <a href="/admin/leads/{{ $lead->id }}/edit">
              @endcan
            @endcan

              {{ $lead->name }}

            @can('edit', $lead)
              @can('update', $lead)
                </a>
              @endcan
            @endcan

          </td>
          <td class="td-action">
          @if($lead->manager->id == 1)
            <a href="#" class="button tiny">Принять</a>
          @endif
          </td>

          <td class="td-phone">{{ decorPhone($lead->phone) }}</td>
          <td class="td-choice">
            {{ $lead->choices_goods_categories->implode('name', ',') }}
            <br>
            {{ $lead->choices_services_categories->implode('name', ',') }}
            <br>
            {{ $lead->choices_raws_categories->implode('name', ',') }}
          </td>

          <td class="td-badget">{{ num_format($lead->badget, 0) }}</td>
          <td class="td-stage">{{ $lead->stage->name }}</td>
          <td class="td-challenge">
              {{ $lead->first_challenge->challenge_type->name or '' }}
          </td>
          <td>
              @if(!empty($lead->first_challenge->deadline_date))
                <span class="">{{ $lead->first_challenge->deadline_date->format('d.m.Y') }}</span><br>
                <span class="tiny-text">{{ $lead->first_challenge->deadline_date->format('H:i') }}</span> 
               @endif
          </td>
          <td class="td-manager">
            @if(!empty($lead->manager->first_name))
            {{ $lead->manager->first_name . ' ' . $lead->manager->second_name }}

            @else
            Не назначен
            @endif
          </td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $lead])

          <td class="td-delete">
            @if (($lead->system_item != 1) && ($lead->god != 1))
            @can('delete', $lead)
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
    <span class="pagination-title">Кол-во записей: {{ $leads->count() }}</span>
    {{ $leads->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')

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
