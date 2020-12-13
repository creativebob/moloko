@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
{{ $estimates->isNotEmpty() ? num_format($estimates->total(), 0) : 0 }}
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('estimates.includes.title')
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">
        <table class="content-table tablesorter estimates" id="content" data-sticky-container data-entity-alias="estimates">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-date">Дата заказа</th>
                    <th class="td-number">Номер заказа</th>
                    <th class="td-name">Клиент</th>
                    <th class="td-phone">Телефон</th>
                    <th class="td-amount">Сумма</th>
                    <th class="td-discount-currency">Скидка</th>
                    <th class="td-total">Сумма к оплате</th>
                    <th class="td-payment">Оплачено</th>
                    @if(extra_right('margin-show'))<th class="td-margin_currency">Маржа</th>@endif
                    <th class="td-partner">Доля партнёра</th>
                    @if(extra_right('share-currency-show'))<th class="td-share-currency">Доля агента</th>@endif
                    @if(extra_right('principal-currency-show'))<th class="td-principal-currency">Доля принципала</th>@endif
                    <th class="td-saled">Продажа</th>
                    <th class="td-dissmissed">Списание</th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

              @if($estimates->isNotEmpty())
              @foreach($estimates as $estimate)
              <tr class="item @if($estimate->moderation == 1)no-moderation @endif" id="estimates-{{ $estimate->id }}" data-name="{{ $estimate->name }}">
                  <td class="td-drop"><div class="sprite icon-drop"></div></td>
                  <td class="td-checkbox checkbox">

                    <input type="checkbox" class="table-check" name="estimate_id" id="check-{{ $estimate->id }}"
                    @if(!empty($filter['booklist']['booklists']['default']))
                    @if (in_array($estimate->id, $filter['booklist']['booklists']['default'])) checked
                    @endif
                    @endif
                    >
                    <label class="label-check" for="check-{{ $estimate->id }}"></label>
                </td>
                <td class="td-date">
                    <span>{{ $estimate->registered_at->format('d.m.Y') }}</span><br>
                    <span class="tiny-text">{{ $estimate->registered_at->format('H:i') }}</span>
                </td>
                <td class="td-number"><a href="/admin/leads/{{ $estimate->lead_id }}/edit">{{ $estimate->number }}</a>
                </td>
                <td class="td-name">
                  <a href="/admin/estimates?client_id={{ $estimate->client_id }}" class="filter_link" title="Фильтровать">
                    {{ $estimate->client->clientable->name ?? 'Имя не указано' }}
                </a>
                <br>
                @isset($estimate->client->clientable->location)
                    <span class="tiny-text">
                        {{ $estimate->client->clientable->location->city->name }}, {{ $estimate->client->clientable->location->address }}
                    </span>
                @endisset
                <td class="td-phone">
                    {{ isset($estimate->client->clientable->main_phone->phone) ? decorPhone($estimate->client->clientable->main_phone->phone) : 'Номер не указан' }}
                    {{-- @if($estimate->client->clientable->email)<br><span class="tiny-text">{{ $estimate->client->clientable->email ?? '' }}</span>@endif --}}
                </td>
                <td class="td-amount">{{ num_format($estimate->amount, 0) }}</td>
                <td class="td-discount-currency">
                  @if($estimate->discount_currency != 0)
                    {{ num_format($estimate->discount_currency, 0) }} <sup> {{ num_format($estimate->discount_percent, 0) }}%</sup>
                  @endif
                </td>
                <td class="td-total">
                  <span class="bold">{{ num_format($estimate->total, 0) }}</span>
                  @if($estimate->total_points != 0)
                    <span class="tiny-text"> | $estimate->total_points</span>
                  @endif

                </td>

                <td class="td-payment">{{ num_format($estimate->payments->sum('total'), 0) }}</td>

                @if(extra_right('margin-show'))
                  <td class="td-margin_currency">{{ num_format($estimate->margin_currency, 0) }} <sup>{{ num_format($estimate->margin_percent, 0) }}%</sup></td>
                @endif

                <td class="td-partner">
                  @if(isset($estimate->agent))
                    @if($estimate->agent->agent_id == $estimate->company_id)
                    
                      {{ num_format($estimate->share_currency, 0) }} <sup>{{ num_format($estimate->share_currency * 100 / $estimate->total, 0) }}%</sup><br>
                      <span class="tiny-text">{{ $estimate->agent->company->name_short ?? $estimate->agent->company->name }} (Агент)</span>

                    @else

                      {{ num_format($estimate->principal_currency, 0) }} <sup>{{ num_format($estimate->principal_currency * 100 / $estimate->total, 0) }}%</sup><br>
                      <span class="tiny-text">{{ $estimate->company->name_short ?? $estimate->company->name }} (Принципал)</span>

                    @endif
                  @endif
                </td>  

                @if(extra_right('share-currency-show'))
                  <td class="td-share-currency">
                    @if(isset($estimate->agent))
                      @if($estimate->agent->agent_id != $estimate->company_id)
                        {{ num_format($estimate->share_currency, 0) }} <sup>{{ num_format($estimate->share_currency * 100 / $estimate->total, 0) }}%</sup>
                      @endif
                    @endif
                  </td>
                @endif


                @if(extra_right('principal-currency-show'))
                  <td class="td-principal-currency">
                    @if(isset($estimate->agent))
                      @if($estimate->agent->agent_id == Auth::user()->company_id)
                        {{ num_format($estimate->share_currency, 0) }} <sup>{{ num_format($estimate->principal_currency * 100 / $estimate->total, 0) }}%</sup>
                      @endif
                    @endif
                  </td>
                @endif        

              <td class="td-saled">{{ ($estimate->conducted_at) ? 'Чек закрыт' : '' }}</td>
              <td class="td-dissmissed">{{ ($estimate->is_dissmissed == 1) ? 'Списан' : '' }}</td>

              {{-- <td class="td-delete">
                  @if ($estimate->system !== 1)
                  @can('delete', $estimate)
                  <a class="icon-delete sprite" data-open="item-delete"></a>
                  @endcan
                  @endif 
              </td> --}}
          </tr>
          @endforeach
          @endif
      </tbody>
            <tfoot>
            @include('estimates.includes.totals')
            </tfoot>

  </table>
</div>
</div>

{{-- Pagination --}}
<div class="grid-x" id="pagination">
  <div class="small-6 cell pagination-head">
    <span class="pagination-title">Кол-во записей: {{ $estimates->count() }}</span>
    {{ $estimates->appends(Request::all())->links() }}
</div>
</div>


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

