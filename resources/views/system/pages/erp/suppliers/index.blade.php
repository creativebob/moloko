@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    @isset($suppliers)
        {{ num_format($suppliers->total(), 0) }}
    @endisset
@endsection

@section('title-content')
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Supplier::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">
            <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="suppliers">
                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name" data-serversort="name">Название поставщика</th>
                    <th class="td-address">Адрес</th>
                    <th class="td-phone">Телефон</th>
                    <th class="td-user_id">Руководитель</th>
                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
                </thead>
                <tbody data-tbodyId="1" class="tbody-width">
                @foreach($suppliers as $supplier)
                    <tr class="item @if(auth()->user()->company_id == $supplier->id)active @endif  @if($supplier->moderation == 1)no-moderation @endif"
                        id="suppliers-{{ $supplier->id }}" data-name="{{ $supplier->company->name }}">
                        <td class="td-drop">
                            <div class="sprite icon-drop"></div>
                        </td>
                        <td class="td-checkbox checkbox">
                            <input type="checkbox" class="table-check" name="supplier_id"
                                   id="check-{{ $supplier->id }}"

                                   {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                                   @if(!empty($filter['booklist']['booklists']['default']))
                                   {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                                   @if (in_array($supplier->id, $filter['booklist']['booklists']['default'])) checked
                                @endif
                                @endif
                            ><label class="label-check" for="check-{{ $supplier->id }}"></label>
                        </td>
                        <td class="td-photo tiny">
                            <img src="{{ getPhotoPath($supplier->company, 'small') }}" alt="">
                        </td>
                        <td class="td-name">
                            @php
                                $edit = 0;
                            @endphp
                            @can('update', $supplier)
                                @php
                                    $edit = 1;
                                @endphp
                            @endcan
                            @if($edit == 1)
                                <a href="suppliers/{{ $supplier->id }}/edit">
                                    @endif
                                    {{ $supplier->company->name }} 

                                        @if(($supplier->company->legal_form)&&($supplier->company->name_legal))
                                            ({{ $supplier->company->legal_form->name}} {{ $supplier->company->name_short ?? $supplier->company->name_legal }})
                                        @endif

                                    @if($edit == 1)
                                </a>
                            @endif
                            <br><span class="tiny-text">{{ $supplier->company->location->country->name }}</span>
                        </td>
                        {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
                        <td class="td-address">@if(!empty($supplier->company->location->address)){{ $supplier->company->location->address }}@endif </td>
                        <td class="td-phone">{{ isset($supplier->company->main_phone->phone) ? decorPhone($supplier->company->main_phone->phone) : 'Номер не указан' }}</td>
                        <td class="td-user_id">{{ $supplier->company->director->first_name or ' ... ' }} {{ $supplier->company->director->second_name or ' ... ' }} </td>

                        {{-- Элементы управления --}}
                        @include('includes.control.table-td', ['item' => $supplier])
                        @include('system.common.includes.control.table_td_archive', ['item' => $supplier])
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="grid-x" id="pagination">
        <div class="small-6 cell pagination-head">
            <span class="pagination-title">Кол-во записей: {{ $suppliers->count() }}</span>
            {{ $suppliers->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
