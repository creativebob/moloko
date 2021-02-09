@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->description }}"/>

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    {{ num_format($shifts->total(), 0) }}
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Shift::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">
            <table class="content-table tablesorter" id="content" data-sticky-container
                   data-entity-alias="shifts">
                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-date">Дата</th>
                    <th class="td-filial_outlet">Филиал / Торговая точка</th>
                    <th class="td-opened">Открытие / закрытие</th>
                    <th class="td-balance">Сумма открытия / закрытия</th>
                    <th class="td-control"></th>
                </tr>
                </thead>
                <tbody data-tbodyId="1" class="tbody-width">
                @if(!empty($shifts))
                    @foreach($shifts as $shift)
                        <tr class="item @if(auth()->user()->company_id == $shift->id)active @endif  @if($shift->moderation == 1)no-moderation @endif"
                            id="shifts-{{ $shift->id }}" data-name="{{ $shift->company->name }}">
                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>
                            <td class="td-checkbox checkbox">
                                <input type="checkbox" class="table-check" name="shift_id"
                                       id="check-{{ $shift->id }}"

                                       {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                                       @if(!empty($filter['booklist']['booklists']['default']))
                                       {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                                       @if (in_array($shift->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                    @endif
                                ><label class="label-check" for="check-{{ $shift->id }}"></label>
                            </td>

                            <td class="td-date">{{ $shift->date->format('d.m.Y') }}</td>
                            <td class="td-filial_outlet">{{ optional($shift->filial)->name }} / {{ optional($shift->outlet)->name }}</td>
                            <td class="td-opened">{{ optional($shift->opened_at)->format('H:i') }} / {{ optional($shift->closed_at)->format('H:i') }}</td>
                            <td class="td-balance">{{ num_format($shift->balance_open, 0) }} / {{ num_format($shift->balance_close, 0) }}</td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $shift])
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
            <span class="pagination-title">Кол-во записей: {{ $shifts->count() }}</span>
            {{ $shifts->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
