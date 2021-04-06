@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->description }}"/>

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    @if(!empty($vendors))
        {{ num_format($vendors->total(), 0) }}
    @endif
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Vendor::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">
            <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="vendors">
                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name=""
                                                               id="check-all"><label class="label-check"
                                                                                     for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name" data-serversort="name">Название продавца</th>
                    <th class="td-address">Адрес</th>
                    <th class="td-phone">Телефон</th>
                    <th class="td-user_id">Руководитель</th>
                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
                </thead>
                <tbody data-tbodyId="1" class="tbody-width">
                @if(!empty($vendors))
                    @foreach($vendors as $vendor)
                        <tr class="item @if(auth()->user()->company_id == $vendor->supplier->company->id)active @endif  @if($vendor->moderation == 1)no-moderation @endif"
                            id="vendors-{{ $vendor->id }}" data-name="{{ $vendor->supplier->company->name }}">
                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>
                            <td class="td-checkbox checkbox">
                                <input type="checkbox" class="table-check" name="vendor_id" id="check-{{ $vendor->id }}"

                                       {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                                       @if(!empty($filter['booklist']['booklists']['default']))
                                       {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                                       @if (in_array($vendor->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                    @endif
                                ><label class="label-check" for="check-{{ $vendor->id }}"></label>
                            </td>
                            <td class="td-photo tiny">
                                <img src="{{ getPhotoPath($vendor->supplier->company, 'small') }}" alt="">
                            </td>
                            <td class="td-name">
                                @php
                                    $edit = 0;
                                @endphp
                                @can('update', $vendor)
                                    @php
                                        $edit = 1;
                                    @endphp
                                @endcan
                                @if($edit == 1)
                                    <a href="vendors/{{ $vendor->id }}/edit">
                                        @endif
                                        {{ $vendor->supplier->company->name }} 

                                        @if(($vendor->company->legal_form)&&($vendor->company->name_legal))
                                            ({{ $vendor->company->legal_form->name}} {{ $vendor->company->name_short ?? $vendor->company->name_legal }})
                                        @endif

                                        @if($edit == 1)
                                    </a>
                                @endif
                                <br><span
                                    class="tiny-text">{{ $vendor->supplier->company->location->country->name }}</span>
                            </td>
                            {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
                            <td class="td-address">@if(!empty($vendor->supplier->company->location->address)){{ $vendor->supplier->company->location->address }}@endif </td>
                            <td class="td-phone">{{ isset($vendor->supplier->company->main_phone->phone) ? decorPhone($vendor->supplier->company->main_phone->phone) : 'Номер не указан' }}</td>
                            <td class="td-user_id">{{ $vendor->supplier->company->director->first_name or ' ... ' }} {{ $vendor->supplier->company->director->second_name or ' ... ' }} </td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $vendor])

                            @include('system.common.includes.control.table_td_archive', ['item' => $vendor])
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
            <span class="pagination-title">Кол-во записей: {{ $vendors->count() }}</span>
            {{ $vendors->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
