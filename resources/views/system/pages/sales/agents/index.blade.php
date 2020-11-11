@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->page_description }}"/>

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    @if(!empty($agents))
        {{ num_format($agents->total(), 0) }}
    @endif
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Agent::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">
            <table class="content-table tablesorter" id="content" data-sticky-container
                   data-entity-alias="agents">
                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name=""
                                                               id="check-all"><label class="label-check"
                                                                                     for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name" data-serversort="name">Название агента</th>
                    <th class="td-sector">Направление</th>
                    <th class="td-phone">Контактный телефон</th>
                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
                </thead>
                <tbody data-tbodyId="1" class="tbody-width">
                @if(!empty($agents))
                    @foreach($agents as $agent)
                        <tr class="item @if(auth()->user()->company_id == $agent->id)active @endif  @if($agent->moderation == 1)no-moderation @endif"
                            id="agents-{{ $agent->id }}" data-name="{{ $agent->company->name }}">
                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>
                            <td class="td-checkbox checkbox">
                                <input type="checkbox" class="table-check" name="agent_id"
                                       id="check-{{ $agent->id }}"

                                       {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                                       @if(!empty($filter['booklist']['booklists']['default']))
                                       {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                                       @if (in_array($agent->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                    @endif
                                ><label class="label-check" for="check-{{ $agent->id }}"></label>
                            </td>
                            <td class="td-photo tiny">
                                <img src="{{ getPhotoPath($agent->company, 'small') }}" alt="">
                            </td>
                            <td class="td-name">
                                @php
                                    $edit = 0;
                                @endphp
                                @can('update', $agent)
                                    @php
                                        $edit = 1;
                                    @endphp
                                @endcan
                                @if($edit == 1)
                                    <a href="agents/{{ $agent->id }}/edit">
                                        @endif
                                        {{ $agent->company->name }} ({{ $agent->company->legal_form->name
                                        ?? '' }})
                                        @if($edit == 1)
                                    </a>
                                @endif
                                <br><span class="tiny-text">{{ $agent->company->location->country->name }}</span>
                            </td>
                            <td class="td-sector">{{ $agent->company->sector->name ?? ' ... ' }} </td>

                            {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
                            <td class="td-phone">{{ isset($agent->company->main_phone->phone) ? decorPhone($agent->company->main_phone->phone) : 'Номер не указан' }}</td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $agent])
                            @include('system.common.includes.control.table_td_archive', ['item' => $agent])
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
            <span class="pagination-title">Кол-во записей: {{ $agents->count() }}</span>
            {{ $agents->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
        </div>
    </div>
@endsection



@push('scripts')
    {{-- Скрипт сортировки и перетаскивания для таблицы --}}
    @include('includes.scripts.tablesorter-script')
    @include('includes.scripts.sortable-table-script')

    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')

    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')

    {{-- Скрипт чекбоксов --}}
    @include('includes.scripts.checkbox-control')
@endpush
