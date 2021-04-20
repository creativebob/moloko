@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('exсel')
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

        $reserves = false;
        if (auth()->user()->staff->first()->filial->outlets->first()) {
            $reserves = auth()->user()->staff->first()->filial->outlets->first()->settings->firstWhere('alias', 'reserves') ? true : false;
        }
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
                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name=""
                                                               id="check-all"><label class="label-check"
                                                                                     for="check-all"></label></th>
                    <th class="td-date">Дата</th>
                    <th class="td-case-number">Номер</th>
                    <th class="td-name">Контакт</th>
                    <th class="td-action"></th>
                    <th class="td-phone">Телефон</th>
                    <th class="td-choice">Спрос</th>
                    <th class="td-badget">Сумма сделки</th>
                    @if($reserves)
                    <th class="td-reserves">Резервы</th>
                    @endif
                    <th class="td-stage">Этап</th>
                    {{-- <th class="td-challenge">Задачи</th> --}}
                    <th class="td-status">Статус</th>
                    <th class="td-shipment_at">Дата отгрузки</th>
                    {{-- <th class="td-deadline_date">Дедлайн</th> --}}

                    <th class="td-city-address">Адрес</th>

                    @if($right_lead_all_managers)
                        <th class="td-manager">Менеджер</th>
                    @endif



                    {{-- <th class="td-control"></th> --}}
                    <th class="td-delete"></th>
                </tr>
                </thead>
                <tbody data-tbodyId="1" class="tbody-width">
                @if(!empty($leads))
                    @foreach($leads as $lead)
                        <tr
                            class="item
                            @if($lead->moderation == 1)no-moderation @endif
                            @if($lead->estimate->debt == 0)paid @endif
                            @if($lead->estimate->is_dismissed)dismissed @endif
                                stage-{{$lead->stage->id }}
                                "
                            id="leads-{{ $lead->id }}"
                            data-name="{{ $lead->name }}"
                            data-entity="leads"
                            data-id="{{ $lead->id }}"
                        >

                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>
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
                                @isset($lead->email)
                                    <br><span class="tiny-text" data-open="modal-send_email">{{ $lead->email }}</span>
                                @endisset
                            </td>
                            <td class="td-choice">
                                {{-- $lead->choice->name ?? '' --}}
                                {{ $lead->estimate->goods_items->implode('goods.article.name', ', ') }}
                            </td>

                            <td class="td-badget">
                                <span class="
                                    @if($lead->estimate->payments->sum('total') < $lead->estimate->total)) text-red @endif
                                    @if($lead->estimate->payments->sum('total') >= $lead->estimate->total) text-green @endif
                                    @if($lead->estimate->total == 0) text-grey @endif">
                                    {{ ($lead->estimate->total > 0) ? num_format($lead->estimate->total, 0) : num_format($lead->badget, 0) }}
                                </span>

                                @if(($lead->estimate->payments->sum('total') > 0) && ($lead->estimate->payments->sum('total') < $lead->estimate->total))
                                    <br><span class="text-mini" title="К доплате: {{ num_format($lead->estimate->debt, 0) }} руб.">{{ num_format($lead->estimate->paid, 0) }}</span>
                                @endif
                            </td>

                            @if($reserves)
                            <td class="td-reserves">{{ num_format($lead->estimate->goods_items->sum('count'), 0) }} / {{ num_format($lead->estimate->goodsItemsReserves, 0) }}</td>
                            @endif

                            <td class="td-stage">{{ $lead->stage->name }}</td>
                            {{-- <td class="td-challenge">
                                <span class="tiny-text">{{ $lead->first_challenge->appointed->second_name or ''}}</span>
                                <span class="tiny-text">{{ $lead->challenges_active_count ?? ''}}</span>
                            </td> --}}

                            <td class="td-status">
                                @if($lead->estimate->conducted_at)
                                    @if($lead->estimate->is_dismissed)
                                        Списан
                                    @else
                                        Чек закрыт
                                    @endif
                                @else
                                    Открыт
                                @endif
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

                            <td class="td-city">
                                {{ $lead->location->city->name }}<br>
                                <span class="tiny-text">{{ $lead->location->address }}</span>

                            </td>

                            @if($right_lead_all_managers)
                                <td class="td-manager">
                                    @if($lead->estimate->agent_id)
                                        @if($lead->estimate->agent->agent_id == \Auth::user()->company_id)
                                            <span class="mark-dark">Получен от: {{ $lead->estimate->company->name_short ?? $lead->estimate->company->name }} </span>
                                        @else
                                            <span class="mark-dark">&#10150; {{ $lead->estimate->agent->company->name_short ?? $lead->estimate->agent->company->name }} </span>
                                        @endif
                                    @else
                                        @if(!empty($lead->manager->first_name))
                                            {{ $lead->manager->first_name . ' ' . $lead->manager->second_name }}
                                        @else
                                            Не назначен
                                        @endif
                                    @endif
                                </td>
                            @endif

                            {{-- Элементы управления --}}
                            {{-- @include('includes.control.table-td', ['item' => $lead]) --}}

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
    @include('includes.modals.send_email')

@endsection

@push('scripts')
    <script type="application/javascript">

        $(document).on('click', '.take-lead', function (event) {
            event.preventDefault();

            $(this).prop('disabled', true);

            var id = $(this).closest('.item').attr('id').split('-')[1];

            $.get("/admin/lead_appointed_check", function (data) {
                if (data === 1) {

                    $.get("/admin/lead_appointed", {
                        id: id
                    }, function (html) {
                        $('#modal').html(html);
                        $('#add-appointed').foundation();
                        $('#add-appointed').foundation('open');
                    });
                } else {

                    $.get("/admin/lead_take", {
                        id: id
                    }, function (data) {
                        const companyName = data.company_name !== null ? data.company_name : "";
                        $('#leads-' + data.id + ' .td-case-number').text(data.case_number);
                        $('#leads-' + data.id + ' .td-name').html('<a href="/admin/leads/' + data.id + '/edit">' + data.name + '</a><br><span class="tiny-text">' + companyName + '</span>');
                        $('#leads-' + data.id + ' .td-action').html('');
                        $('#leads-' + data.id + ' .td-manager').text(data.manager);
                    });
                }
                ;
            });
            /* Act on the event */
        });

        $(document).on('click', '#submit-appointed', function (event) {
            event.preventDefault();

            $(this).prop('disabled', true);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/admin/lead_distribute",
                type: "POST",
                data: $(this).closest('form').serialize(),
                success: function (date) {

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
        $(document).on('click', '.remove-modal, .submit-edit, .submit-add, .submit-appointed', function () {
            $(this).closest('.reveal-overlay').remove();
        });

    </script>
    {{-- Скрипт сортировки и перетаскивания для таблицы --}}
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
    @include('includes.scripts.delete-ajax-script')
@endpush
