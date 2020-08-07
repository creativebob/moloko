@extends('layouts.app')

@section('inhead')

@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('exel')
<!-- <a href="/admin/leads?calls=yes" class="button tiny">Перезвоны</a> -->
@endsection

@section('planfact')
{{-- {{ link_to_route('plans.show', 'План', $parameters = ['alias' => 'leads'], $attributes = ['class' => 'button tiny']) }}
{{ link_to_route('statistics.show', 'Факт', $parameters = ['alias' => 'leads'], $attributes = ['class' => 'button tiny']) }} --}}
@endsection

@section('content-count')
{{-- Количество элементов --}}
@if(!empty($leads))
{{ num_format($leads->total(), 0) }}
@endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('leads.includes.title')
@endsection

@section('content')

@php
  $right_lead_regular = extra_right('lead-regular');
  $right_lead_dealer = extra_right('lead-dealer');
  $right_lead_service = extra_right('lead-service');
  $right_lead_all_managers = extra_right('lead-all-managers');
@endphp

<div class="grid-x" id="pagination">
    <div class="small-6 cell pagination-head">
        {{ $leads->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
    </div>
</div>

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter leads" id="content" data-sticky-container data-entity-alias="leads">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-date">Дата</th>
          <th class="td-case-number">Номер</th>
          <th class="td-name">Контакт</th>
          <th class="td-action"></th>
          <th class="td-phone">Телефон</th>
          <th class="td-choice">Спрос</th>
          <th class="td-badget">Сумма сделки</th>
          <th class="td-stage">Этап</th>
          <th class="td-challenge">Задачи</th>
          <th class="td-status">Статус</th>
          <th class="td-shipment_at">Дата отгрузки</th>
          {{-- <th class="td-deadline_date">Дедлайн</th> --}}

          @if($right_lead_all_managers)
          <th class="td-manager">Менеджер</th>
          @endif

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

            @can('view', $lead)
            <a href="/admin/leads/{{ $lead->id }}/edit">{{ $lead->name ?? 'Имя не указано' }}</a>
            @else
              {{ $lead->name ?? 'Имя не указано'}}
            @endcan

            <br>
            <span class="tiny-text">{{ $lead->company_name ?? '' }}</span>

          </td>
          <td class="td-action">
            @if($lead->manager_id == 1)

            @if(($lead->lead_type_id == 1)&&($right_lead_regular))
            <button class="button tiny take-lead">Принять</button>
            @endif

            @if(($lead->lead_type_id == 2)&&($right_lead_dealer))
            <button class="button tiny take-lead">Принять (Дилер)</button>
            @endif

            @if(($lead->lead_type_id == 3)&&($right_lead_service))
            <button class="button tiny take-lead">Принять (Сервисный центр)</button>
            @endif

            @endif
          </td>

          <td class="td-phone">
            {{ isset($lead->main_phone->phone) ? decorPhone($lead->main_phone->phone) : 'Номер не указан' }}
            @if($lead->email)<br><span class="tiny-text">{{ $lead->email ?? '' }}</span>@endif
          </td>
          <td class="td-choice">
            {{-- $lead->choice->name ?? '' --}}
            {{ $lead->estimate->goods_items->implode('goods.article.name', ', ') }}
          </td>

          <td class="td-badget">
            <span class="
            @if(($lead->payment > 0)&&($lead->payment < $lead->badget)) text-red @endif
            @if($lead->payment == $lead->badget) text-green @endif

            ">{{ num_format($lead->badget, 0) }}</span>

            @if(($lead->payment != $lead->badget)&&($lead->payment != 0))
              <br><span class="tiny-text">{{ num_format($lead->payment, 0) }}</span>
            @endif
          </td>

          <td class="td-stage">{{ $lead->stage->name }}</td>
          <td class="td-challenge">
                    {{-- $lead->first_challenge->challenge_type->name or '' }}<br>
                    <span class="tiny-text">{{ $lead->first_challenge->appointed->second_name or ''}}</span> --}}
                    <span class="tiny-text">{{ $lead->challenges_active_count ?? ''}}</span>

                  </td>

          <td class="td-status">
            @if($lead->estimate->is_saled) Чек закрыт @else Открыт @endif
          </td>

           <td class="td-shipment_at">
            @if($lead->shipment_at)
              <span>{{ $lead->shipment_at->format('d.m.Y') }}</span><br>
              <span class="tiny-text">{{ $lead->shipment_at->format('H:i') }}</span>
            @endif
          </td>

                {{-- <td>
                    @if(!empty($lead->first_challenge->deadline_date))
                    <span class="">{{ $lead->first_challenge->deadline_date->format('d.m.Y') }}</span><br>
                    <span class="tiny-text">{{ $lead->first_challenge->deadline_date->format('H:i') }}</span>
                    @endif
                  </td> --}}

                  @if($right_lead_all_managers)
                  <td class="td-manager">
                    @if(!empty($lead->manager->first_name))
                    {{ $lead->manager->first_name . ' ' . $lead->manager->second_name }}

                    @else
                    Не назначен
                    @endif
                  </td>
                  @endif


                  {{-- Элементы управления --}}
                  @include('includes.control.table-td', ['item' => $lead])

                  <td class="td-delete">
                    @if (($lead->system != 1) && ($lead->god != 1))
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
            {{ $leads->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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

        <script type="application/javascript">

          $(document).on('click', '.take-lead', function(event) {
            event.preventDefault();

            $(this).prop('disabled', true);

            var id = $(this).closest('.item').attr('id').split('-')[1];

            $.get("/admin/lead_appointed_check", function(data){
                if (data === 1) {

                  $.get("/admin/lead_appointed", {id: id}, function(html){
                      $('#modal').html(html);
                      $('#add-appointed').foundation();
                      $('#add-appointed').foundation('open');
                  });
                } else {

                  $.get("/admin/lead_take", {id: id}, function(date){
                      $('#leads-' + date.id + ' .td-case-number').text(date.case_number);
                      $('#leads-' + date.id + ' .td-name').html('<a href="/admin/leads/' + date.id + '/edit">' + date.name + '</a>');
                      $('#leads-' + date.id + ' .td-action').html('');
                      $('#leads-' + date.id + ' .td-manager').text(date.manager);
                  });
                };
            });
            /* Act on the event */
          });

          $(document).on('click', '#submit-appointed', function(event) {
            event.preventDefault();

            $(this).prop('disabled', true);

            $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "/admin/lead_distribute",
              type: "POST",
              data: $(this).closest('form').serialize(),
              success: function(date){

                $('#add-appointed').foundation('close');

                var result = $.parseJSON(date);

                $('#leads-' + result.id + ' .td-case-number').text(result.case_number);
                $('#leads-' + result.id + ' .td-name').html('<a href="/admin/leads/' + result.id + '/edit">' + result.name + '</a>');
                $('#leads-' + result.id + ' .td-action').html('');
                $('#leads-' + result.id + ' .td-manager').text(result.manager);

              }
            });
          });

  // $(document).on('click', '.take-lead', function(event) {
  //   event.preventDefault();

  //   var entity_alias = $(this).closest('.item').attr('id').split('-')[0];
  //   var id = $(this).closest('.item').attr('id').split('-')[1];
  //   var item = $(this);

  //   $.ajax({
  //     headers: {
  //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //     },
  //     url: "/admin/lead_take",
  //     type: "POST",
  //     data: {id: id},
  //     success: function(date){
  //       var result = $.parseJSON(date);
  //       // alert(result);

  //       $('#leads-' + result.id + ' .td-case-number').text(result.case_number);
  //       $('#leads-' + result.id + ' .td-name').html('<a href="/admin/leads/' + result.id + '/edit">' + result.name + '</a>');
  //       $('#leads-' + result.id + ' .td-action').html('');
  //       $('#leads-' + result.id + ' .td-manager').text(result.manager);
  //     }
  //   });


  //   /* Act on the event */
  // });

// ---------------------------------- Закрытие модалки -----------------------------------
$(document).on('click', '.remove-modal, .submit-edit, .submit-add, .submit-appointed', function() {
  $(this).closest('.reveal-overlay').remove();
});

</script>
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
